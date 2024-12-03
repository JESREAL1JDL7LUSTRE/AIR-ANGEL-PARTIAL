<?php
ob_start(); // Start output buffering
session_start();
include 'db.php'; // Include database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Employee_Email = trim($_POST['Email']);
    $Department = trim($_POST['Department']);
    $Employee_First_Name = trim($_POST['First_Name']);
    $Employee_Last_Name = trim($_POST['Last_Name']);
    $Employee_Middle_Name = trim($_POST['Middle_Name']);
    $Employee_Birthday = trim($_POST['Birthday']);
    $Employee_Nationality = trim($_POST['Nationality']);
    $Employee_Sex = trim($_POST['Sex']);
    $Employee_Address = trim($_POST['Address']);
    $Employee_Salary = trim($_POST['Salary']);
    $Employee_Health_Insurance = trim($_POST['Health_Insurance']);
    $Employee_PhoneNumber = trim($_POST['Phone_Number']);
    $Employee_Emergency_Contact_No = trim($_POST['Emergency_Contact_No']);

    // Validate phone numbers
    if (!is_numeric($Employee_PhoneNumber) || strlen($Employee_PhoneNumber) > 11) {
        echo "Phone Number must be numeric and not exceed 11 digits.";
        exit();
    }
    if (!is_numeric($Employee_Emergency_Contact_No) || strlen($Employee_Emergency_Contact_No) > 11) {
        echo "Emergency Contact Number must be numeric and not exceed 11 digits.";
        exit();
    }

    // Prepare SQL to insert a new employee into the database
    $sql = "INSERT INTO Employees 
        (Employee_Email, Department, Employee_Last_Name, Employee_First_Name, Employee_Middle_Name, Employee_Birthday, Employee_Nationality, Employee_Sex, Employee_Address, Employee_PhoneNumber, Employee_Emergency_Contact_No, Employee_Salary, Employee_Health_Insurance) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Bind the parameters to the query
    $stmt->bind_param(
        "ssssssssssdss",
        $Employee_Email,
        $Department,
        $Employee_Last_Name,
        $Employee_First_Name,
        $Employee_Middle_Name,
        $Employee_Birthday,
        $Employee_Nationality,
        $Employee_Sex,
        $Employee_Address,
        $Employee_PhoneNumber,
        $Employee_Emergency_Contact_No,
        $Employee_Salary,
        $Employee_Health_Insurance
    );

    // Execute the statement
    if ($stmt->execute()) {
        echo "Employee added successfully!";
        header("Location: employees.php"); // Redirect to admin page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch all employees for display
$sql = "SELECT * FROM Employees";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Employee</title>
    <link rel="stylesheet" href="/ANGEL/styles/signup.css">
    <script>
        // Show or hide the admin signup form
        function toggleAdminForm() {
            var form = document.getElementById("adminSignupForm");
            form.style.display = form.style.display === "none" || form.style.display === "" ? "block" : "none";
        }
    </script>
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
    <button onclick="toggleAdminForm()">Add Employee</button>

    <!-- Add Employee Form -->
    <div id="adminSignupForm" style="display:none;">
        <h2>Create an Admin Employee</h2>
        <form method="POST">
            <input type="text" name="First_Name" placeholder="First Name" required>
            <input type="text" name="Last_Name" placeholder="Last Name" required>
            <input type="text" name="Middle_Name" placeholder="Middle Name">
            <input type="email" name="Email" placeholder="Email" required>
            <input type="date" name="Birthday" required>
            <input type="text" name="Nationality" placeholder="Nationality" required>
            <input type="text" name="Sex" placeholder="Sex" required>
            <input type="text" name="Address" placeholder="Address" required>
            <input type="number" step="0.01" name="Salary" placeholder="Salary" required>
            <input type="text" name="Health_Insurance" placeholder="Health Insurance" required>
            <input type="text" name="Phone_Number" placeholder="Phone Number" maxlength="11" required>
            <input type="text" name="Emergency_Contact_No" placeholder="Emergency Contact Number" maxlength="11" required>
            <input type="text" name="Department" placeholder="Department" required>
            <button type="submit">Add</button>
        </form>
    </div>

    <h2>All Employees</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Sex</th>
            <th>Birthday</th>
            <th>Nationality</th>
            <th>Salary</th>
            <th>Health Insurance</th>
            <th>Phone Number</th>
            <th>Emergency Contact No</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Employee_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Last_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_First_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Middle_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Email']); ?></td>
                    <td><?php echo htmlspecialchars($row['Department']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Sex']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Birthday']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Nationality']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Salary']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Health_Insurance']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_PhoneNumber']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Emergency_Contact_No']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="13">No employees found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
