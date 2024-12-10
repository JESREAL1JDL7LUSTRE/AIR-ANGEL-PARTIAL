<?php
ob_start();
session_start();
include('db.php'); // Include your database connection

// Ensure the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Handle form submission to update user info
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the "Save" button was pressed
    if (isset($_POST['save'])) {
        if (isset($_SESSION['Account_Email'])) {
        // Get the updated data from the form
        $first_name = $_POST['Account_First_Name'];
        $last_name = $_POST['Account_Last_Name'];
        $middle_name = $_POST['Account_Middle_Name'];
        $email = $_POST['Account_Email'];
        $phone_number = $_POST['Account_PhoneNumber'];
        $username = $_POST['Username'];
        $birthday = $_POST['Account_Birthday'];
        $sex = $_POST['Account_Sex'];
        $account_id = $_POST['Account_ID']; // Ensure Account_ID is passed for the update

        // Prepare the SQL query to update the account info
        $sql_update = "UPDATE Account 
                        SET Account_First_Name = ?, 
                            Account_Last_Name = ?, 
                            Account_Email = ?, 
                            Account_PhoneNumber = ?, 
                            Username = ?, 
                            Account_Middle_Name = ?, 
                            Account_Birthday = ?, 
                            Account_Sex = ? 
                        WHERE Account_ID = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone_number, $username, $middle_name, $birthday, $sex, $account_id);

        // Execute the update query
        if ($stmt->execute()) {
            echo "<script>alert('Account updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating account!');</script>";
        }
    }
}
}
// Get logged-in user email
$user_email = $_SESSION['Account_Email'];

// Fetch user account information from the database
$sql_user_info = "SELECT * FROM Account WHERE Account_Email = ?";
$stmt = $conn->prepare($sql_user_info);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user_result = $stmt->get_result();

// Fetch booked flights and passenger details from reservation_to_account
$sql_booked_flights = "
SELECT DISTINCT
    rta.Reservation_to_Account_ID AS Booking_ID,
    r.Reservation_ID,
    r.Payment_ID_FK AS Payment_ID,
    af.Flight_Number,
    af.Departure_Date,
    af.Arrival_Date,
    af.Origin,
    af.Destination,
    af.Departure_Time,
    af.Arrival_Time,
    af.Amount,
    p.Payment_Amount,
    p.Payment_Date,
    p.Payment_Method_Name,
    pass.Passenger_Last_Name,
    pass.Passenger_First_Name,
    pass.Passenger_Middle_Name,
    pass.Passenger_Birthday,
    pass.Passenger_Nationality,
    pass.Passenger_Email,
    pass.Passenger_PhoneNumber,
    pass.Passenger_Emgergency_Contact_No
FROM reservation_to_account rta
INNER JOIN reservation r ON rta.Reservation_ID_FK = r.Reservation_ID
INNER JOIN flight_to_reservation_to_passenger frp ON frp.Flight_to_Reservation_ID_FK = r.Reservation_ID
INNER JOIN available_flights af ON af.Available_Flights_Number_ID = frp.Available_Flights_Number_ID_FK
LEFT JOIN payment p ON r.Payment_ID_FK = p.Payment_ID
LEFT JOIN reservation_to_passenger rtp ON r.Reservation_ID = rtp.Reservation_ID_FK
LEFT JOIN passenger pass ON rtp.Passenger_ID_FK = pass.Passenger_ID
WHERE rta.Account_ID_FK = (SELECT Account_ID FROM Account WHERE Account_Email = ?)";

$stmt_flights = $conn->prepare($sql_booked_flights);
$stmt_flights->bind_param("s", $user_email);
$stmt_flights->execute();
$flights_result = $stmt_flights->get_result();

if (isset($_POST['print_ticket'])) {

    require_once __DIR__ . '/../vendor/autoload.php';

    // If the form is submitted to download the ticket
    $sql_account_print_eticket = "
    SELECT DISTINCT
        rta.Reservation_to_Account_ID AS Booking_ID,
        r.Reservation_ID,
        r.Payment_ID_FK AS Payment_ID,
        af.Flight_Number,
        af.Departure_Date,
        af.Arrival_Date,
        af.Origin,
        af.Destination,
        af.Departure_Time,
        af.Arrival_Time,
        af.Amount AS Flight_Amount,
        p.Payment_Amount,
        p.Payment_Date,
        p.Payment_Method_Name,
        pass.Passenger_Last_Name,
        pass.Passenger_First_Name,
        pass.Passenger_Middle_Name
    FROM reservation_to_account rta
    INNER JOIN reservation r ON rta.Reservation_ID_FK = r.Reservation_ID
    INNER JOIN flight_to_reservation_to_passenger frp ON frp.Flight_to_Reservation_ID_FK = r.Reservation_ID
    INNER JOIN available_flights af ON af.Available_Flights_Number_ID = frp.Available_Flights_Number_ID_FK
    LEFT JOIN payment p ON r.Payment_ID_FK = p.Payment_ID
    LEFT JOIN reservation_to_passenger rtp ON r.Reservation_ID = rtp.Reservation_ID_FK
    LEFT JOIN passenger pass ON rtp.Passenger_ID_FK = pass.Passenger_ID
    WHERE rta.Account_ID_FK = (SELECT Account_ID FROM Account WHERE Account_Email = ?)
    ";

    $stmt = $conn->prepare($sql_account_print_eticket);
    $stmt->execute([$_SESSION['Account_Email']]);
    $ticketDetails = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ticketDetails[] = $row;
    }

    // Check if ticketDetails is empty before proceeding
    if (empty($ticketDetails)) {
        die("No ticket details found.");
    }

    // Initialize total amount
    $totalAmount = 0;
    $flightAmount = $ticketDetails[0]['Flight_Amount']; // The amount for the flight (assuming it's the same for all passengers)

    // Fetch add-ons separately for each passenger
    $addons = [];
    foreach ($ticketDetails as $ticket) {
        $sql_addons = "
        SELECT
            add_on.Seat_Selector_ID_FK,
            add_on.Food_ID_FK,
            add_on.Baggage_ID_FK,
            ss.Seat_Selector_Number AS Seat_Selector_Name,
            ss.Price AS Seat_Selector_Price,
            f.Food_Name AS Food_Name,
            f.Price AS Food_Price,
            b.Baggage_Weight AS Baggage_Name,
            b.Price AS Baggage_Price
        FROM add_on
        LEFT JOIN seat_selector ss ON ss.Seat_Selector_ID = add_on.Seat_Selector_ID_FK
        LEFT JOIN food f ON f.Food_ID = add_on.Food_ID_FK
        LEFT JOIN baggage b ON b.Baggage_ID = add_on.Baggage_ID_FK
        WHERE add_on.FRP_Number_ID_FK = ?
        ";

        // Prepare the query for each reservation's FRP_Number_ID
        $stmt_addons = $conn->prepare($sql_addons);
        $stmt_addons->execute([$ticket['Booking_ID']]);
        $addonDetails = $stmt_addons->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Add each passenger's add-ons to the addons array
        $addons[$ticket['Booking_ID']] = $addonDetails;
    }

    // Calculate total amount, add-ons, and passengers
    $totalAmount = 0;
    $flightAmount = $ticketDetails[0]['Flight_Amount'];
    $passengerNames = [];
    $addonPrices = [];

    foreach ($ticketDetails as $ticket) {
        // Passenger name
        $passengerNames[] = $ticket['Passenger_First_Name'] . ' ' . $ticket['Passenger_Last_Name'];
        
        // Get add-ons for this passenger
        $passengerAddons = isset($addons[$ticket['Booking_ID']]) ? $addons[$ticket['Booking_ID']] : [];
        
        // Calculate total add-ons for this passenger
        $totalAddons = 0;
        foreach ($passengerAddons as $addon) {
            // Check for non-null add-ons and print only valid ones
            if ($addon['Seat_Selector_ID_FK'] !== null) {
                $totalAddons += $addon['Seat_Selector_Price'];
            }
            if ($addon['Food_ID_FK'] !== null) {
                $totalAddons += $addon['Food_Price'];
            }
            if ($addon['Baggage_ID_FK'] !== null) {
                $totalAddons += $addon['Baggage_Price'];
            }
        }
        
        $addonPrices[] = $totalAddons;
        $totalAmount += $flightAmount + $totalAddons;
    }

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "Air Angel - E-Ticket", 0, 1, 'C');

    // Flight Information
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Flight Number: " . $ticketDetails[0]['Flight_Number'], 0, 1);
    $pdf->Cell(0, 10, "Origin: " . $ticketDetails[0]['Origin'], 0, 1);
    $pdf->Cell(0, 10, "Destination: " . $ticketDetails[0]['Destination'], 0, 1);
    $pdf->Cell(0, 10, "Departure Time: " . $ticketDetails[0]['Departure_Time'], 0, 1);
    $pdf->Cell(0, 10, "Arrival Time: " . $ticketDetails[0]['Arrival_Time'], 0, 1);
    $pdf->Cell(0, 10, "Amount: \$" . number_format($flightAmount, 2) . " per passenger", 0, 1);

    // Add-ons
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Add-ons:", 0, 1);
    foreach ($addons as $bookingID => $addonDetails) {
        foreach ($addonDetails as $addon) {
            // Print seat add-on if it exists
            if ($addon['Seat_Selector_ID_FK'] !== null) {
                $pdf->Cell(0, 10, "" . $addon['Seat_Selector_Name'] . " - \$" . number_format($addon['Seat_Selector_Price'], 2), 0, 1);
            }
            // Print food add-on if it exists
            if ($addon['Food_ID_FK'] !== null) {
                $pdf->Cell(0, 10, "" . $addon['Food_Name'] . " - \$" . number_format($addon['Food_Price'], 2), 0, 1);
            }
            // Print baggage add-on if it exists
            if ($addon['Baggage_ID_FK'] !== null) {
                $pdf->Cell(0, 10, "" . $addon['Baggage_Name'] . " - \$" . number_format($addon['Baggage_Price'], 2), 0, 1);
            }
        }
    }

    // Passenger Information
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Passenger(s):", 0, 1);
    foreach ($passengerNames as $index => $name) {
        $pdf->Cell(0, 10, ($index + 1) . ". $name", 0, 1);
    }

    // Total Amount
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Total Amount: \$" . number_format($totalAmount, 2), 0, 1);

    // Save and download PDF
    $ticketDir = $_SERVER['DOCUMENT_ROOT'] . '/ANGEL/etickets/';
    if (!is_dir($ticketDir)) {
        mkdir($ticketDir, 0777, true);
    }

    $ticketFile = $ticketDir . "eticket_" . $ticketDetails[0]['Reservation_ID'] . ".pdf";
    $pdf->Output($ticketFile, 'F'); // Save PDF to server

    // Send the file for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($ticketFile) . '"');
    header('Content-Length: ' . filesize($ticketFile));
    readfile($ticketFile);

    unlink($ticketFile); // Delete the file after sending
    exit;
}

// Check if a delete request has been made
if (isset($_GET['delete'])) {
    $delete_reservation_id = $_GET['delete'];

    // Delete the record from reservation_to_passenger (if applicable)
    $sql_delete_passenger = "DELETE FROM reservation_to_passenger WHERE Reservation_ID_FK IN (SELECT Reservation_ID_FK FROM reservation_to_account WHERE Reservation_to_Account_ID = ?)";
    $stmt_delete_passenger = $conn->prepare($sql_delete_passenger);
    $stmt_delete_passenger->bind_param("i", $delete_reservation_id);
    $stmt_delete_passenger->execute();

    // Delete the record from reservation_to_account
    $sql_delete_account = "DELETE FROM reservation_to_account WHERE Reservation_to_Account_ID = ?";
    $stmt_delete_account = $conn->prepare($sql_delete_account);
    $stmt_delete_account->bind_param("i", $delete_reservation_id);
    
    if ($stmt_delete_account->execute()) {
        echo "Booking deleted successfully.";
        // Redirect to avoid resubmission of the form
        header("Location: acc_account.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="/ANGEL/styles/account.css">
</head>
<body>
    <h1>Account</h1>

    <ul>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="acc_account.php">Account</a></li>
        <li><a href="acc_dashboard.php">Home</a></li>
    </ul>

    <h2>Welcome User!</h2>
    
    <!-- User Information Form -->
    <h3>User Information</h3>
    <form method="POST">
        <table border="1">
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Username</th>
                <th>Birthday</th>
                <th>Sex</th>
                <th>Action</th>
            </tr>
            <?php if ($user_result && $user_result->num_rows > 0): ?>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="text" name="Account_Last_Name" value="<?php echo htmlspecialchars($row['Account_Last_Name']); ?>" required class="view-only" id="Account_Last_Name" readonly></td>
                        <td><input type="text" name="Account_First_Name" value="<?php echo htmlspecialchars($row['Account_First_Name']); ?>" required class="view-only" id="Account_First_Name" readonly></td>
                        <td><input type="text" name="Account_Middle_Name" value="<?php echo htmlspecialchars($row['Account_Middle_Name']); ?>" class="view-only" id="Account_Middle_Name" readonly></td>
                        <td><input type="email" name="Account_Email" value="<?php echo htmlspecialchars($row['Account_Email']); ?>" required class="view-only" id="Account_Email" readonly></td>
                        <td><input type="text" name="Account_PhoneNumber" value="<?php echo htmlspecialchars($row['Account_PhoneNumber']); ?>" class="view-only" id="Account_PhoneNumber" readonly></td>
                        <td><input type="text" name="Username" value="<?php echo htmlspecialchars($row['Username']); ?>" required class="view-only" id="Username" readonly></td>
                        <td><input type="date" name="Account_Birthday" value="<?php echo htmlspecialchars($row['Account_Birthday']); ?>" class="view-only" id="Account_Birthday" readonly></td>

                        <td>
                            <div class="radio-group">
                                <input type="radio" id="Male" name="Account_Sex" value="Male" <?php echo ($row['Account_Sex'] == 'Male') ? 'checked' : ''; ?> class="view-only" disabled>
                                <label for="Male">Male</label>

                                <input type="radio" id="Female" name="Account_Sex" value="Female" <?php echo ($row['Account_Sex'] == 'Female') ? 'checked' : ''; ?> class="view-only" disabled>
                                <label for="Female">Female</label>
                            </div>
                        </td>

                        <td>
                            <input type="hidden" name="Account_ID" value="<?php echo $row['Account_ID']; ?>"> <!-- Pass Account_ID for update -->
                            <button type="button" id="editButton">Edit</button>
                            <button type="submit" name="save" id="saveButton" style="display: none;">Save</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No user information found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </form>

    <h4>Booked Flights</h4>
<table border="1">
    <tr>
        <th>Booking ID</th>
        <th>Passenger Name</th>
        <th>Passenger Email</th>
        <th>Passenger Phone</th>
        <th>Reservation_ID</th>
        <th>Flight Number</th>
        <th>Departure Date</th>
        <th>Arrival Date</th>
        <th>Origin</th>
        <th>Destination</th>
        <th>Amount</th>
        <th>Payment ID</th>
        <th>Payment Date</th>
        <th>Payment Method</th>
        <th>Actions</th>
    </tr>

    <?php if ($flights_result && $flights_result->num_rows > 0): ?>
        <?php while ($row = $flights_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Booking_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Passenger_First_Name'] . ' ' . $row['Passenger_Last_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Passenger_Email']); ?></td>
                <td><?php echo htmlspecialchars($row['Passenger_PhoneNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['Reservation_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Flight_Number']); ?></td>
                <td><?php echo htmlspecialchars($row['Departure_Date']); ?></td>
                <td><?php echo htmlspecialchars($row['Arrival_Date']); ?></td>
                <td><?php echo htmlspecialchars($row['Origin']); ?></td>
                <td><?php echo htmlspecialchars($row['Destination']); ?></td>
                <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                <td><?php echo htmlspecialchars($row['Payment_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Payment_Date']); ?></td>
                <td><?php echo htmlspecialchars($row['Payment_Method_Name']); ?></td>
                <td>
                    <!-- Delete button with confirmation -->
                    <a href="?delete=<?php echo $row['Booking_ID']; ?>" onclick="return confirm('Are you sure you want to delete this booking?');">
                        <button type="button">Delete</button>
                    </a>
                    <form method="POST">
                    <button type="submit" name="print_ticket">Print Ticket</button>
                    </form>
                </td>


            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="18">No booked flights found.</td>
        </tr>
    <?php endif; ?>
</table>


    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            // Enable inputs and show the save button
            var inputs = document.querySelectorAll('.view-only');
            inputs.forEach(function(input) {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.classList.add('editable');
            });
            document.getElementById('saveButton').style.display = 'inline-block';
            document.getElementById('editButton').style.display = 'none';
        });
    </script>
</body>
</html>
