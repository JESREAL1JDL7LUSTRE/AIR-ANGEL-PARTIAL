<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: signin.php");  // Redirect if not admin
    exit();
}

// Fetch all users from the database (or any other data you need)
$result = $conn->query("SELECT * FROM Account");

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
    <a href="logout.php">Logout</a>
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Username</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['Account_ID']; ?></td>
                <td><?php echo $row['Account_Last_Name']; ?></td>
                <td><?php echo $row['Account_First_Name']; ?></td>
                <td><?php echo $row['Account_Email']; ?></td>
                <td><?php echo $row['Account_PhoneNumber']; ?></td>
                <td><?php echo $row['Username']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
