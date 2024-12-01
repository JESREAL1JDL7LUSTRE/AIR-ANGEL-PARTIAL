<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Email = trim($_POST['Account_Email']);
    $Password = trim($_POST['Password']);

    // Admin credentials
    $boss_email = 'admin@boss.com';
    $boss_password = 'jesreal';

    // Check if admin credentials are used
    if ($email === $boss_email && $password === $boss_password) {
        // Admin login
        $_SESSION['user_role'] = 'admin';
        $_SESSION['email'] = $email;
        header('Location: admin.php'); // Redirect to admin dashboard
        exit;
    } else {
        // Check the database for normal user credentials
        $stmt = $conn->prepare("SELECT * FROM Account WHERE Account_Email = ?");
        $stmt->bind_param("s", $Account_Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($Password, $user['Password'])) {
                // User login
                $_SESSION['Account_Email'] = $user['Account_Email'];
                $_SESSION['Account_ID'] = $user['Account_ID'];
                header('Location: user_dashboard.php'); // Redirect to user dashboard
                exit;
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "Invalid email or password.";
        }
    }
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
        <input type="email" name="Account_Email" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="Password" required><br>
        
        <button type="submit">Sign In</button>
    </form>
</body>
</html>
