<?php include 'db.php'; ?> 
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Last_Name = $_POST['Last_Name'];  // Fixed input name
    $Account_First_Name = $_POST['First_Name']; // Fixed input name
    $Account_Email = $_POST['Email'];
    $Account_PhoneNumber = $_POST['Phone_Number']; // Fixed input name
    $Username = $_POST['Username'];
    $Password = $_POST['Password'];
    $Confirm_Password = $_POST['Confirm_Password']; // Get the confirm password field

    // Check if passwords match
    if ($Password !== $Confirm_Password) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($Password, PASSWORD_BCRYPT);

    // Use prepared statements to insert user
    $stmt = $conn->prepare("INSERT INTO Account (Account_Last_Name, Account_First_Name, Account_Email, Account_PhoneNumber, Username, Password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $Account_Last_Name, $Account_First_Name, $Account_Email, $Account_PhoneNumber, $Username, $hashed_password);

    if ($stmt->execute()) {
        echo "User added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel Sign Up</title>
</head>
<body>
    <h1>Create a New Account</h1>
    <form method="POST">
        <label>Last Name:</label>
        <input type="text" name="Last_Name" required><br> <!-- Fixed name -->
        <label>First Name:</label>
        <input type="text" name="First_Name" required><br> <!-- Fixed name -->
        <label>Email:</label>
        <input type="email" name="Email" required><br>
        <label>Phone Number:</label>
        <input type="text" name="Phone_Number" required><br> <!-- Fixed name -->
        <label>Username:</label>
        <input type="text" name="Username" required><br>
        <label>Password:</label>
        <input type="password" name="Password" required><br>
        <label>Confirm Password:</label>
        <input type="password" name="Confirm_Password" required><br> <!-- Confirm password field -->
        <button type="submit">Create Account</button>
    </form>
    
    <br>

    <a href="signin.php">Already a member? Sign In</a> <!-- Sign In link -->

    <br><br>
    <a href="index.php" style="margin-bottom: 20px; display: inline-block;">&larr; Back </a>
</body>
</html>
