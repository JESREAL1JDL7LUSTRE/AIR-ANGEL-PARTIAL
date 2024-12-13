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
        // Delete related records from child tables
        $sql_delete_reservation_to_account = "DELETE FROM reservation_to_account WHERE Reservation_ID_FK = ?";
        $stmt1 = $conn->prepare($sql_delete_reservation_to_account);
        $stmt1->bind_param("i", $reservation_id);
        $stmt1->execute();

        $sql_delete_reservation_to_passenger = "DELETE FROM reservation_to_passenger WHERE Reservation_ID_FK = ?";
        $stmt2 = $conn->prepare($sql_delete_reservation_to_passenger);
        $stmt2->bind_param("i", $reservation_id);
        $stmt2->execute();

        $sql_delete_flight_to_reservation = "DELETE FROM flight_to_reservation_to_passenger WHERE Flight_to_Reservation_ID_FK = ?";
        $stmt3 = $conn->prepare($sql_delete_flight_to_reservation);
        $stmt3->bind_param("i", $reservation_id);
        $stmt3->execute();

        // Delete the reservation from the reservation table
        $sql_delete_reservation = "DELETE FROM Reservation WHERE Reservation_ID = ?";
        $stmt4 = $conn->prepare($sql_delete_reservation);
        $stmt4->bind_param("i", $reservation_id);
        $stmt4->execute();

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
        af.Flight_Number, 
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
        af.Flight_Number LIKE ? OR
        pass.Passenger_Last_Name LIKE ? OR
        pass.Passenger_ID LIKE ?
";

$stmt = $conn->prepare($sql);
$search_term = '%' . $search_query . '%';
$stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
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
    <link rel="stylesheet" href="/ANGEL/styles/admin.css">
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <header>
        <h1>All Reservations</h1>
    </header>
    <main>
        <!-- Search Bar -->
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by any field..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (count($reservations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Booking Date</th>
                        <th>Flight Number</th>
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
                            <td><?php echo htmlspecialchars($reservation['Flight_Number'] ?: 'N/A'); ?></td>
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
