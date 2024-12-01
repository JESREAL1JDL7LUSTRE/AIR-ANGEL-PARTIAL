<?php
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page if not an admin
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <limk rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Welcome Admin!</h1>
    <a href="logout.php">Logout</a>
    <a href="admin_see_accounts.php">See all users</a>
    <a href="admin_add_flights.php">Add flights</a>
    <a href="see_flights.php">See flights</a>
</body>
</html>
