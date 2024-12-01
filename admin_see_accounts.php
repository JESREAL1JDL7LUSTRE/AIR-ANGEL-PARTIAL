<?php
session_start();
include 'db.php';


// Fetch all users from the database
$sql = "SELECT Account_ID, Account_Last_Name, Account_First_Name, Account_Email, Account_PhoneNumber, Username FROM Account";
$result = $conn->query($sql);
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
    <a href="admin_see_accounts.php">See all users</a>
    <a href="admin_add_flights.php">Add flights</a>
    <a href="see_flights.php">See flights</a>
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
   
</body>
</html>
