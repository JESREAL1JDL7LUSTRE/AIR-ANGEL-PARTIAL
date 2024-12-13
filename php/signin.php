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
                    header('Location: acc_dashboard.php'); // Redirect to user dashboard
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
            window.history.back();
        }

        // Function to toggle password visibility
        function togglePassword() {
            var password = document.getElementById("password");
            var toggleButton = document.getElementById("togglePasswordBtn");

            if (password.type === "password") {
                password.type = "text";
                toggleButton.innerText = "Hide Password";
            } else {
                password.type = "password";
                toggleButton.innerText = "Show Password";
            }
        }
    </script>
    <link rel="stylesheet" href="/ANGEL/styles/signin.css">
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
</head>
<body>
    <header>
        <div class="logo">
            <img src="/ANGEL/assets/images/logo.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="noacc_dashboard.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="signin-form-container">
            <h1>Sign In</h1>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="Account_Email" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="Password" required><br>

                <!-- Button to toggle password visibility -->
                <button type="button" id="togglePasswordBtn" onclick="togglePassword()">Show Password</button><br>

                <button type="submit">Sign In</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </div>
    </main>
</body>
</html>
