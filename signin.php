<?php include 'db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Email = $_POST['Email']; // Using correct form field name
    $Password = $_POST['Password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT Password FROM Account WHERE Account_Email = ?");
    $stmt->bind_param("s", $Account_Email); // Using correct variable
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verify password
    if ($hashed_password && password_verify($Password, $hashed_password)) {
        // Start a session before setting the session variable
        session_start();
        $_SESSION['user_email'] = $Account_Email;
        header("Location: dashboard.php"); // Redirect after successful sign-in
        exit(); // Prevent further code execution
    } else {
        echo "Invalid email or password.";
    }

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
        <label>Email:</label>
        <input type="email" name="Email" required><br> <!-- Fixed name -->
        <label>Password:</label>
        <input type="password" name="Password" required><br> <!-- Fixed name -->
        <button type="submit">Sign In</button>
    </form>
    <a href="signup.php" style="margin-top: 20px; display: inline-block;">Don't have an account? Register here</a>
</body>
</html>
