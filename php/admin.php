<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Account_Email']) && $_SESSION['Is_Admin'] != 1) {
    header('Location: signin.php');
    exit;
}
// Queries and their labels
$queries = [
    'Total Users' => 'SELECT COUNT(*) AS count FROM Account WHERE Is_Admin = 0',
    'Total Admins' => 'SELECT COUNT(*) AS count FROM Account WHERE Is_Admin = 1',
    'Total Flights' => 'SELECT COUNT(*) AS count FROM Available_Flights',
    'Total Reservations' => 'SELECT COUNT(*) AS count FROM Reservation',
    'Total Employees' => 'SELECT COUNT(*) AS count FROM Employees',
];

// Execute queries and store results
$counts = [];
foreach ($queries as $label => $sql) {
    $result = $conn->query($sql);
    if ($result) {
        $counts[$label] = $result->fetch_assoc()['count'];
    } else {
        $counts[$label] = 'Error'; // Handle query error gracefully
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
        <nav>
            <ul>
                <li><a href="admin.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/ANGEL/styles/admin.css">

</head>
<bod>
    <header>
        
    </header>
    <h1>Welcome Admin!</h1>
        <a href="see_flights.php" class="button">Available Flights (<?php echo htmlspecialchars($counts['Total Flights']); ?>)</a>
        <a href="see_reservations.php" class="button">Available Reservations (<?php echo htmlspecialchars($counts['Total Reservations']); ?>)</a>
        <a href="employees.php" class="button">Employees (<?php echo htmlspecialchars($counts['Total Employees']); ?>)</a>
        <a href="admin_see_accounts.php" class="button">Users (<?php echo htmlspecialchars($counts['Total Users']); ?>)</a>
        <a href="add_admins.php" class="button">Admins (<?php echo htmlspecialchars($counts['Total Admins']); ?>)</a>
        <a href="admin_add_ad_ons.php" class="button">Add-ons</a>
        <a href="admin_add_flights.php" class="button">Add Flight</a>
        <a href="employee_assign.php" class="button">Assign Employees</a>

</body>
</html>
