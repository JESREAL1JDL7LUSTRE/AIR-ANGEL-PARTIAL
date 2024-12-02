<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php'; // Include your database connection

// Check if the form is submitted and both fields are filled
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Account_Email = $_POST['Account_Email'] ?? '';
    $Password = $_POST['Password'] ?? '';

    // Check if both fields are filled
    if (!empty($Account_Email) && !empty($Password)) {
        // Check the database for user credentials
        $stmt = $conn->prepare("SELECT * FROM Account WHERE Account_Email = ?");
        $stmt->bind_param("s", $Account_Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch the user data

        // Check if the user is an admin
        if ($user['Is_Admin'] === 1) {
            // Admin login logic
            if (password_verify($Password, $user['Password'])) {
                // Set session variables for the admin
                $_SESSION['Account_Email'] = $user['Account_Email'];
                $_SESSION['Account_ID'] = $user['Account_ID'];
                $_SESSION['Is_Admin'] = 1; // Mark the user as an admin in the session

                // Redirect to see_flights.php
                header('Location: admin.php');
                exit;
            } else {
                $error = "Invalid email or password.";
            }
            } else {
                // Regular user login logic
                if (password_verify($Password, $user['Password'])) {
                    $_SESSION['Account_Email'] = $user['Account_Email'];
                    $_SESSION['Account_ID'] = $user['Account_ID'];
                    header('Location: user_dashboard.php'); // Redirect to user dashboard
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            }
        } else {
            $error = "No user found with that email.";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <script>
        function goBack() {
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
    <h1>Sign In</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="Account_Email" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="Password" required><br>
        
        <button type="submit">Sign In</button>
    </form>
    <h2> </h2>
    <button type="button" onclick="goBack()">Go Back</button>
    
    <p>Dont have an Account? <a href="signup.php">Sign up</a></p>
</body>
</html>
