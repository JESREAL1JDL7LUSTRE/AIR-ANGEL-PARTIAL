<?php include 'db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Last_Name = $_POST['Last Name'];
    $Account_First_Name = $_POST['First Name'];
    $Account_Email = $_POST['Email'];
    $Account_PhoneNumber = $_POST['Phone Number'];
    $Username = $_POST['Username'];
    $Password = $_POST['Password'];

    $conn->query("INSERT INTO Account (Account_Last_Name, Account_First_Name, Account_Email, Account_PhoneNumber, Username, Password) VALUES ('$Account_Last_Name', '$Account_First_Name', '$Account_Email', '$Account_PhoneNumber', '$Username', '$Password')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel Sign In</title>
</head>
<body>
    <h1>Add New User</h1>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" required><br>
        <button type="submit">Add User</button>
    </form>
	
	<a href="user_index.php" style="margin-bottom: 20px; display: inline-block;">&larr; Back </a>

</body>
</html>
