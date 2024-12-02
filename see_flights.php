<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Is_Admin']) || $_SESSION['Is_Admin'] !== 1) {
    header('Location: signin.php'); // Redirect to login page if not an admin
    exit;
}


// Fetch all users from the database
$sql = "SELECT Available_Flights_Number_ID, Departure_Date, Arrival_Date, Origin, Destination, Departure_Time, Arrival_Time, Amount FROM Available_Flights";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script>
        function goBack() {
            window.location.href = 'admin.php';
        }
    </script>
</head>
<body>
    <h1>Welcome Admin!</h1>
            <a href="logout.php">Logout</a> <!-- Show Logout if logged in -->
            <a href="admin_see_accounts.php">See all users</a>
            <a href="admin_add_flights.php">Add flights</a>
            <a href="see_flights.php">See flights</a>
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>Available_Flights_Number_ID</th>
            <th>Departure_Date</th>
            <th>Arrival_Date</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Departure_Time</th>
            <th>Arrival_Time</th>
            <th>Amount</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Available_Flights_Number_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Departure_Date']); ?></td>
                    <td><?php echo htmlspecialchars($row['Arrival_Date']); ?></td>
                    <td><?php echo htmlspecialchars($row['Origin']); ?></td>
                    <td><?php echo htmlspecialchars($row['Destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['Departure_Time']); ?></td>
                    <td><?php echo htmlspecialchars($row['Arrival_Time']); ?></td>
                    <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No users found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <h2> </h2>
    <button type="button" onclick="goBack()">Go Back</button>
</body>
</html>
