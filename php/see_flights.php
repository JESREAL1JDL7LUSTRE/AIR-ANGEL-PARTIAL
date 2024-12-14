<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Is_Admin']) || $_SESSION['Is_Admin'] !== 1) {
    header('Location: signin.php'); // Redirect to login page if not an admin
    exit;
}

// Handle flight updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['Account_Email'])) {
        // Handle update logic here (as in your code)
    }
}

// Handle flight deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Handle delete logic here (as in your code)
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Available_Flights_Number_ID'])) {
    // Get form data
    $flightID = $_POST['Available_Flights_Number_ID'];
    $flightNumber = $_POST['Flight_Number'];
    $departureDate = $_POST['Departure_Date'];
    $arrivalDate = $_POST['Arrival_Date'];
    $origin = $_POST['Origin'];
    $destination = $_POST['Destination'];
    $departureTime = $_POST['Departure_Time'];
    $arrivalTime = $_POST['Arrival_Time'];
    $amount = $_POST['Amount'];

    // Sanitize inputs (for security)
    $flightID = $conn->real_escape_string($flightID);
    $flightNumber = $conn->real_escape_string($flightNumber);
    $departureDate = $conn->real_escape_string($departureDate);
    $arrivalDate = $conn->real_escape_string($arrivalDate);
    $origin = $conn->real_escape_string($origin);
    $destination = $conn->real_escape_string($destination);
    $departureTime = $conn->real_escape_string($departureTime);
    $arrivalTime = $conn->real_escape_string($arrivalTime);
    $amount = $conn->real_escape_string($amount);

    // Prepare the SQL Update Query
    $sql = "
        UPDATE available_flights 
        SET 
            Flight_Number = '$flightNumber',
            Departure_Date = '$departureDate',
            Arrival_Date = '$arrivalDate',
            Origin = '$origin',
            Destination = '$destination',
            Departure_Time = '$departureTime',
            Arrival_Time = '$arrivalTime',
            Amount = '$amount'
        WHERE 
            Available_Flights_Number_ID = '$flightID'
    ";

    // Execute the query and handle errors
    if ($conn->query($sql) === TRUE) {
        echo "Flight details updated successfully!";
    } else {
        echo "Error updating flight: " . $conn->error;
    }
}

// Fetch flights with optional search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

$sql = "
    SELECT 
        Available_Flights_Number_ID, 
        Flight_Number, 
        Departure_Date, 
        Arrival_Date, 
        Origin, 
        Destination, 
        Departure_Time, 
        Arrival_Time, 
        Amount 
    FROM Available_Flights
    WHERE 
        Flight_Number LIKE ? OR 
        Departure_Date LIKE ? OR 
        Arrival_Date LIKE ? OR 
        Origin LIKE ? OR 
        Destination LIKE ?
";

$stmt = $conn->prepare($sql);
$search_term = '%' . $search_query . '%';
$stmt->bind_param('sssss', $search_term, $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ANGEL/styles/cards.css">
    <title>Admin Dashboard</title>
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
    <h1>All Available Flights</h1>
    <form method="GET" action="">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by Flight Number, Origin, Destination..." 
            value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
    <a href="admin_add_flights.php" class="add-button">Add Flight</a>
</div>

    <table border="1">
        <tr>
            <th>Flight ID</th>
            <th>Flight No.</th>
            <th>Departure Date</th>
            <th>Arrival Date</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="">
                        <td><?php echo htmlspecialchars($row['Available_Flights_Number_ID']); ?></td>
                        <td><input type="text" name="Flight_Number" value="<?php echo htmlspecialchars($row['Flight_Number']); ?>" required class="view-only" readonly></td>
                        <td><input type="date" name="Departure_Date" value="<?php echo htmlspecialchars($row['Departure_Date']); ?>" required class="view-only" readonly></td>
                        <td><input type="date" name="Arrival_Date" value="<?php echo htmlspecialchars($row['Arrival_Date']); ?>" class="view-only" readonly></td>
                        <td><input type="text" name="Origin" value="<?php echo htmlspecialchars($row['Origin']); ?>" required class="view-only" readonly></td>
                        <td><input type="text" name="Destination" value="<?php echo htmlspecialchars($row['Destination']); ?>" class="view-only" readonly></td>
                        <td><input type="time" name="Departure_Time" value="<?php echo htmlspecialchars($row['Departure_Time']); ?>" required class="view-only" readonly></td>
                        <td><input type="time" name="Arrival_Time" value="<?php echo htmlspecialchars($row['Arrival_Time']); ?>" class="view-only" readonly></td>
                        <td><input type="text" name="Amount" value="<?php echo htmlspecialchars($row['Amount']); ?>" class="view-only" readonly></td>
                        <td>
                            <input type="hidden" name="Available_Flights_Number_ID" value="<?php echo $row['Available_Flights_Number_ID']; ?>">
                            <button type="button" class="editButton">Edit</button>
                            <button type="submit" class="saveButton" style="display: none;">Save</button>
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this flight?')">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No flights found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script>
        const editButtons = document.querySelectorAll('.editButton');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const inputs = row.querySelectorAll('.view-only');
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.classList.add('editable');
                });
                row.querySelector('.saveButton').style.display = 'inline-block';
                this.style.display = 'none';
            });
        });
    </script>
</body>
</html>
