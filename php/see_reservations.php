<?php
ob_start();
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Account_Email']) || $_SESSION['Is_Admin'] != 1) {
    header('Location: signin.php');
    exit;
}

// Handle the deletion of a reservation
if (isset($_GET['delete'])) {
    $reservation_id = $_GET['delete'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Step 1: Delete from the add_on table (child of flight_to_reservation_to_passenger)
        $sql_delete_add_on = "DELETE FROM add_on WHERE FRP_Number_ID_FK IN (SELECT FRP_Number_ID FROM flight_to_reservation_to_passenger WHERE Flight_to_Reservation_ID_FK IN (SELECT Reservation_to_Passenger_ID FROM reservation_to_passenger WHERE Reservation_ID_FK = ?))";
        $stmt1 = $conn->prepare($sql_delete_add_on);
        $stmt1->bind_param("i", $reservation_id);
        $stmt1->execute();

        // Step 2: Delete from flight_to_reservation_to_passenger (child of reservation_to_passenger)
        $sql_delete_flight_to_reservation = "DELETE FROM flight_to_reservation_to_passenger WHERE Flight_to_Reservation_ID_FK IN (SELECT Reservation_to_Passenger_ID FROM reservation_to_passenger WHERE Reservation_ID_FK = ?)";
        $stmt2 = $conn->prepare($sql_delete_flight_to_reservation);
        $stmt2->bind_param("i", $reservation_id);
        $stmt2->execute();

        // Step 3: Delete from reservation_to_passenger
        $sql_delete_reservation_to_passenger = "DELETE FROM reservation_to_passenger WHERE Reservation_ID_FK = ?";
        $stmt3 = $conn->prepare($sql_delete_reservation_to_passenger);
        $stmt3->bind_param("i", $reservation_id);
        $stmt3->execute();

        // Step 4: Delete from reservation_to_account
        $sql_delete_reservation_to_account = "DELETE FROM reservation_to_account WHERE Reservation_ID_FK = ?";
        $stmt4 = $conn->prepare($sql_delete_reservation_to_account);
        $stmt4->bind_param("i", $reservation_id);
        $stmt4->execute();

        // Step 5: Delete the reservation from the reservation table
        $sql_delete_reservation = "DELETE FROM Reservation WHERE Reservation_ID = ?";
        $stmt5 = $conn->prepare($sql_delete_reservation);
        $stmt5->bind_param("i", $reservation_id);
        $stmt5->execute();

        // Commit the transaction
        $conn->commit();

        echo "Reservation deleted successfully.";
        // Redirect to avoid resubmission of the form
        header("Location: see_reservations.php");
        exit();
    } catch (Exception $e) {
        // Roll back the transaction if an error occurs
        $conn->rollback();
        echo "Error deleting reservation: " . $e->getMessage();
    }
}

// Fetch reservations along with flight and passenger information
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

$sql = "
    SELECT 
        r.Reservation_ID, 
        r.Booking_date, 
        r.Payment_ID_FK,
        af.Flight_Number, 
        pass.Passenger_First_Name,
        pass.Passenger_Last_Name, 
        pass.Passenger_ID
    FROM Reservation r
    LEFT JOIN flight_to_reservation_to_passenger frp ON frp.Flight_to_Reservation_ID_FK = r.Reservation_ID
    LEFT JOIN available_flights af ON af.Available_Flights_Number_ID = frp.Available_Flights_Number_ID_FK
    LEFT JOIN reservation_to_passenger rtp ON rtp.Reservation_ID_FK = r.Reservation_ID
    LEFT JOIN passenger pass ON pass.Passenger_ID = rtp.Passenger_ID_FK
    WHERE 
        r.Reservation_ID LIKE ? OR
        r.Booking_date LIKE ? OR
        r.Payment_ID_FK LIKE ? OR
        af.Flight_Number LIKE ? OR
        pass.Passenger_First_Name LIKE ? OR
        pass.Passenger_Last_Name LIKE ? OR
        pass.Passenger_ID LIKE ?
";

$stmt = $conn->prepare($sql);
$search_term = '%' . $search_query . '%';
$stmt->bind_param("sssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Reservations</title>
    <link rel="stylesheet" href="/ANGEL/styles/cards.css">
</head>
<body>
<nav class="navbar">
    <div class="logo-container">
        <img src="/ANGEL/assets/images/logo.png" alt="AirAngel Logo" id="logo-img">
        <h1>Air Angel</h1>
    </div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="see_flights.php">Flights</a></li>
        <li><a href="see_reservations.php">Reservations</a></li>
        <li><a href="admin_see_accounts.php">Users</a></li>
        <li><a href="admin_add_ad_ons.php">Add-ons</a></li>
        <li><a href="employees.php">Employees</a></li>
    </ul>
    <ul class="logout">
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<div class="actions">
    <h1>Reservations</h1>
    <form method="GET" action="">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by Field..." 
            value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
    <a href="noacc_dashboard.php" class="add-button">Add Reservation</a>
</div>

<?php if (count($reservations) > 0): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Reservation ID</th>
                <th>Booking Date</th>
                <th>Payment ID</th>
                <th>Flight Number</th>
                <th>Passenger First Name</th>
                <th>Passenger Last Name</th>
                <th>Passenger ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['Reservation_ID']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Booking_date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Payment_ID_FK'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Flight_Number'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Passenger_First_Name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Passenger_Last_Name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($reservation['Passenger_ID'] ?: 'N/A'); ?></td>
                    <td>
                        <!-- Delete button with confirmation -->
                        <a href="?delete=<?php echo $reservation['Reservation_ID']; ?>" onclick="return confirm('Are you sure you want to delete this reservation?');">
                            <button type="button">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No reservations found.</p>
<?php endif; ?>


    </main>
</body>
</html>
