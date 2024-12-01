<?php
ob_start();  // Start output buffering to ensure no output before header()
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
    <script>
        function goBack() {
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
    <h1>Create an Account</h1>
    <form method="POST">
        <label>First Name:</label><br>
        <input type="text" name="First_Name" required><br>
        
        <label>Last Name:</label><br>
        <input type="text" name="Last_Name" required><br>
        
        <label>Username:</label><br>
        <input type="text" name="Username" required><br>
        
        <label>Phone Number:</label><br>
        <input type="text" name="Phone_Number" required><br>
        
        <label>Email:</label><br>
        <input type="email" name="Email" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="Password" required><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="Confirm_Password" required><br>
        
        <button type="submit">Sign Up</button>
    </form>
    <h2> </h2>
    <button type="button" onclick="goBack()">Go Back</button>
    <p>Already have an account? <a href="signin.php">Sign in</a></p>
</body>
</html>
