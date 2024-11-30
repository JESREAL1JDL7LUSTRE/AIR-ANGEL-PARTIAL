<?php
session_start();
include 'db.php'; // Include your database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Email = $_POST['Email'];
    $Password = $_POST['Password'];
    $Confirm_Password = $_POST['Confirm_Password'];
    $Account_First_Name = $_POST['First_Name'];
    $Account_Last_Name = $_POST['Last_Name'];
    $Account_PhoneNumber = $_POST['Phone_Number'];
    $Username = $_POST['Username'];

    // Check if passwords match
    if ($Password !== $Confirm_Password) {
        echo "Passwords do not match. Please try again.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($Password, PASSWORD_BCRYPT);

    // Check if this is the first user
    $sql_check_first_user = "SELECT COUNT(*) AS total FROM Account";
    $result = $conn->query($sql_check_first_user);
    $row = $result->fetch_assoc();
    $is_admin = ($row['total'] == 0) ? 1 : 0;  // Make the first user an admin

    // Insert the new user into the database
    $sql = "INSERT INTO Account (Account_Email, Password, Account_First_Name, Account_Last_Name, Account_PhoneNumber, Username, is_admin) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $Account_Email, $hashed_password, $Account_First_Name, $Account_Last_Name, $Account_PhoneNumber, $Username, $is_admin);

    if ($stmt->execute()) {
        echo "Account created successfully!";
        header("Location: signin.php");  // Redirect to sign in page after successful registration
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <h1>Create an Account</h1>
    <form method="POST">
        <label>First Name:</label><br>
        <input type="text" name="First_Name" required><br>
        
        <label>Last Name:</label><br>
        <input type="text" name="Last_Name" required><br>
        
        <label>Email:</label><br>
        <input type="email" name="Email" required><br>
        
        <label>Phone Number:</label><br>
        <input type="text" name="Phone_Number" required><br>
        
        <label>Username:</label><br>
        <input type="text" name="Username" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="Password" required><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="Confirm_Password" required><br>
        
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
