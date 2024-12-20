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
    $sql = "INSERT INTO Account (Account_Email, Password, Account_First_Name, Account_Last_Name, Account_PhoneNumber, Username, Is_Admin) 
            VALUES (?, ?, ?, ?, ?, ?, 1)"; // Assuming "Is_Admin" is set to 1 for admin

    $stmt = $conn->prepare($sql);

    // Bind the parameters to the query
    $stmt->bind_param("ssssss", $Account_Email, $hashed_password, $Account_First_Name, $Account_Last_Name, $Account_PhoneNumber, $Username);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Account created successfully!";
        header("Location: admin.php"); // Redirect to admin page after successful registration
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch all users for display
$sql = "SELECT * FROM Account WHERE Is_Admin = 1";
$result = $conn->query($sql);
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

        function togglePassword() {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirmPassword");
            var toggleButton = document.getElementById("togglePasswordBtn");

            if (password.type === "password" && confirmPassword.type === "password") {
                password.type = "text";
                confirmPassword.type = "text";
                toggleButton.innerText = "Hide Password";
            } else {
                password.type = "password";
                confirmPassword.type = "password";
                toggleButton.innerText = "Show Password";
            }
        }

        // Show or hide the admin signup form
        function toggleAdminForm() {
            var form = document.getElementById("adminSignupForm");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
    </script>
    <link rel="stylesheet" href="/ANGEL/styles/signup.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="/ANGEL/assets/images/logo.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
 
        <h1>Welcome Admin!</h1>
        <a href="admin.php">Home</a>
        <nav><button type="button" onclick="toggleAdminForm()">Add Admin</button></nav>

        <!-- Admin Signup Form (Initially Hidden) -->
        <div id="adminSignupForm" style="display:none;">
            <h2>Create an Admin Account</h2>
            <form method="POST">
                <label for="firstName">First Name:</label><br>
                <input type="text" id="firstName" name="First_Name" required><br>

                <label for="lastName">Last Name:</label><br>
                <input type="text" id="lastName" name="Last_Name" required><br>

                <label for="username">Username:</label><br>
                <input type="text" id="username" name="Username" required><br>

                <label for="phoneNumber">Phone Number:</label><br>
                <input type="text" id="phoneNumber" name="Phone_Number" required><br>

                <label for="email">Email:</label><br>
                <input type="email" id="email" name="Email" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="Password" required><br>

                <label for="confirmPassword">Confirm Password:</label><br>
                <input type="password" id="confirmPassword" name="Confirm_Password" required><br>

                <button type="button" id="togglePasswordBtn" onclick="togglePassword()">Show Password</button><br><br>

                <button type="submit">Add Admin</button>
            </form>
        </div>

        <h2>All Users</h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Username</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Account_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_Last_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_First_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_Email']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>
</body>
</html>
