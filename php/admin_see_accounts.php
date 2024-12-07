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
$sql = "SELECT Account_ID, Account_Last_Name, Account_First_Name, Account_Email, Account_PhoneNumber, Username FROM Account WHERE Is_Admin = 0";
$result = $conn->query($sql);

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
        <link rel="stylesheet" href="/ANGEL/styles/admin_see_accounts.css">

</head>
<body>
    <h1>Welcome Admin!</h1>
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

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Account_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Account_Last_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Account_First_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Account_Email']); ?></td>
                    <td><?php echo htmlspecialchars($row['Account_PhoneNumber']); ?></td>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No users found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <h2> </h2>
</body>
</html>
