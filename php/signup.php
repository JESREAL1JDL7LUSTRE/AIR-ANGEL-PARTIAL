<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Email = trim($_POST['Email']);
    $Password = trim($_POST['Password']);
    $Confirm_Password = trim($_POST['Confirm_Password']);
    $Account_First_Name = trim($_POST['First_Name']);
    $Account_Last_Name = trim($_POST['Last_Name']);
    $Account_PhoneNumber = trim($_POST['Phone_Number']);
    $Username = trim($_POST['Username']);

    // Validate phone number
    if (!is_numeric($Account_PhoneNumber)) {
        echo "Phone number must be numeric.";
        exit();
    }

    // Check if passwords match
    if ($Password !== $Confirm_Password) {
        echo "Passwords do not match. Please try again.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($Password, PASSWORD_BCRYPT);

    // Prepare SQL to insert a new user into the database
    $sql = "INSERT INTO Account (Account_Email, Password, Account_First_Name, Account_Last_Name, Account_PhoneNumber, Username) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Bind the parameters to the query
    $stmt->bind_param("ssssss", $Account_Email, $hashed_password, $Account_First_Name, $Account_Last_Name, $Account_PhoneNumber, $Username);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Account created successfully!";
        header("Location: signin.php"); // Redirect to sign-in page after successful registration
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
    <link rel="stylesheet" href="signup.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <h1>Create an Account</h1>
        <form method="POST">
            <div class="form-group">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="First_Name" required>
            </div>
            <div class="form-group">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="Last_Name" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="Username" required>
            </div>
            <div class="form-group">
                <label for="phone-number">Phone Number:</label>
                <input type="text" id="phone-number" name="Phone_Number" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="Password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="Confirm_Password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>