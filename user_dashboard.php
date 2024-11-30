<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: signin.php");  // Redirect if not logged in
    exit();
}

// Get the logged-in user's email
$user_email = $_SESSION['user_email'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM Account WHERE Account_Email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome <?php echo $user['Account_First_Name']; ?>!</h1>
    <a href="logout.php">Logout</a>
    <h2>Your Account Details</h2>
    <p><strong>Email:</strong> <?php echo $user['Account_Email']; ?></p>
    <p><strong>Phone Number:</strong> <?php echo $user['Account_PhoneNumber']; ?></p>
    <p><strong>Username:</strong> <?php echo $user['Username']; ?></p>
</body>
</html>
