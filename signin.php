<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    // Prepare and execute query to fetch user data based on email
    $sql = "SELECT Account_ID, Account_Email, Password, Account_First_Name, Account_Last_Name, Account_PhoneNumber, Username, is_admin FROM Account WHERE Account_Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Bind the result to variables
    $stmt->store_result();
    $stmt->bind_result($Account_ID, $Account_Email, $hashed_password, $Account_First_Name, $Account_Last_Name, $Account_PhoneNumber, $Username, $is_admin);

    // Check if the user exists and validate the password
    if ($stmt->fetch()) {
        // Check if the password is correct
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start session and set session variables
            $_SESSION['user_email'] = $Account_Email;
            $_SESSION['is_admin'] = $is_admin;
            $_SESSION['user_first_name'] = $Account_First_Name;
            $_SESSION['user_last_name'] = $Account_Last_Name;
            $_SESSION['user_id'] = $Account_ID;

            // Redirect based on whether the user is an admin
            if ($is_admin == 1) {
                header("Location: admin_dashboard.php");  // Admin dashboard
            } else {
                header("Location: user_dashboard.php");  // User dashboard
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Invalid email!";
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
</head>
<body>
    <h1>Sign In</h1>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="Email" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="Password" required><br>
        
        <button type="submit">Sign In</button>
    </form>
</body>
</html>
