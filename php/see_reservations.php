<?php
ob_start();
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Account_Email']) || $_SESSION['Is_Admin'] != 1) {
    header('Location: signin.php');
    exit;
}

// Fetch reservations
$sql = "SELECT Reservation_ID, Booking_date FROM Reservation";
$result = $conn->query($sql);
$reservations = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
} elseif (!$result) {
    die("Error fetching reservations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Reservations</title>
    <link rel="stylesheet" href="/ANGEL/styles/admin.css">
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
        <?php if (count($reservations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['Reservation_ID']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['Booking_date']); ?></td>
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
