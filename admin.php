<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Account_Email']) && $_SESSION['Is_Admin'] != 1) {
    header('Location: signin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome Admin!</h1>
            <a href="logout.php">Logout</a> <!-- Show Logout if logged in -->
            <a href="admin_see_accounts.php">See all users</a>
            <a href="admin_add_flights.php">Add flights</a>
            <a href="see_flights.php">See flights</a>
</body>
</html>
