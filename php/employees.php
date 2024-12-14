<?php
ob_start(); // Start output buffering
session_start();
include 'db.php'; // Include database connection

// Check if the form is submitted to add an employee
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adding new employee
    if (isset($_POST['add_employee'])) {
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

    // Deleting an employee
    if (isset($_POST['delete_employee'])) {
        $Employee_ID = $_POST['Employee_ID'];

        // Prepare the SQL query to delete the employee
        $deleteSql = "DELETE FROM Employees WHERE Employee_ID = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $Employee_ID);

        if ($stmt->execute()) {
            echo "Employee deleted successfully!";
            header("Location: employees.php"); // Redirect to refresh the page
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Handle employee save (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_employee'])) {
    $employeeID = $_POST['Employee_ID'];
    $lastName = trim($_POST['Employee_Last_Name']);
    $firstName = trim($_POST['Employee_First_Name']);
    $middleName = trim($_POST['Employee_Middle_Name']);
    $email = trim($_POST['Employee_Email']);
    $department = trim($_POST['Department']);
    $sex = trim($_POST['Employee_Sex']);
    $birthday = trim($_POST['Employee_Birthday']);
    $nationality = trim($_POST['Employee_Nationality']);
    $address = trim($_POST['Employee_Address']); // Missing in your code
    $salary = trim($_POST['Employee_Salary']);
    $healthInsurance = trim($_POST['Employee_Health_Insurance']);
    $phoneNumber = trim($_POST['Employee_PhoneNumber']);
    $emergencyContact = trim($_POST['Employee_Emergency_Contact_No']);

    // Update query
    $sql = "UPDATE Employees 
        SET Department = ?, Employee_Last_Name = ?, Employee_First_Name = ?, 
            Employee_Middle_Name = ?, Employee_Birthday = ?, Employee_Nationality = ?, 
            Employee_Sex = ?, Employee_Address = ?, Employee_PhoneNumber = ?, 
            Employee_Emergency_Contact_No = ?, Employee_Salary = ?, 
            Employee_Health_Insurance = ?, Employee_Email = ? 
        WHERE Employee_ID = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "sssssssssssssi", // Add 'i' for the Employee_ID (integer)
        $department,
        $lastName,
        $firstName,
        $middleName,
        $birthday,
        $nationality,
        $sex,
        $address, // Added missing parameter
        $phoneNumber,
        $emergencyContact,
        $salary,
        $healthInsurance,
        $email,
        $employeeID // Employee_ID as the WHERE condition
    );

    // Execute and handle the result
    if ($stmt->execute()) {
        echo "Employee updated successfully!";
        header("Location: employees.php");
        exit();
    } else {
        echo "Error updating employee: " . $stmt->error;
    }

    $stmt->close();
}

// Search functionality
$searchQuery = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = '%' . $conn->real_escape_string(trim($_GET['search'])) . '%';
    $searchQuery = "WHERE Employee_First_Name LIKE '$search' OR 
                           Employee_Last_Name LIKE '$search' OR 
                           Employee_Email LIKE '$search' OR 
                           Department LIKE '$search'";
}

$sql = "SELECT * FROM Employees $searchQuery";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Employee</title>
    <link rel="stylesheet" href="/ANGEL/styles/cards.css">
    <script>
        // Show or hide the admin signup form
        function toggleAdminForm() {
            var form = document.getElementById("adminSignupForm");
            form.style.display = form.style.display === "none" || form.style.display === "" ? "block" : "none";
        }
    </script>
</head>
<nav class="navbar">
    <div class="logo-container">
        <img src="/ANGEL/assets/images/logo.png" alt="AirAngel Logo" id="logo-img">
        <h1>Air Angel</h1>
    </div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="see_flights.php">Flights</a></li>
        <li><a href="see_reservations.php">Reservations</a></li>
        <li><a href="admin_see_accounts.php">Users</a></li>
        <li><a href="admin_add_ad_ons.php">Add-ons</a></li>
        <li><a href="employees.php">Employees</a></li>
    </ul>
    <ul class="logout">
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<div id="adminSignupForm" style="display:none;">
        <h2 style="text-align: center;">Add New Employee</h2>
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
            <button type="submit" style="background-color: #233D2C; color: white; margin-top: 20px;">Add</button>
        </form>
    </div>
<div class="actions">
    <h1>Employees</h1>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search employees by name, email, or department" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>
    <a class="add-button" onclick="toggleAdminForm()">Add Employee</a></div>  
    
</div>
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
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="">
                    <td><?php echo htmlspecialchars($row['Employee_ID']); ?></td>
                    <td><input type="text" name="Employee_Last_Name" value="<?php echo htmlspecialchars($row['Employee_Last_Name']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_First_Name" value="<?php echo htmlspecialchars($row['Employee_First_Name']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_Middle_Name" value="<?php echo htmlspecialchars($row['Employee_Middle_Name']); ?>" readonly class="view-only"></td>
                    <td><input type="email" name="Employee_Email" value="<?php echo htmlspecialchars($row['Employee_Email']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Department" value="<?php echo htmlspecialchars($row['Department']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_Sex" value="<?php echo htmlspecialchars($row['Employee_Sex']); ?>" readonly class="view-only"></td>
                    <td><input type="date" name="Employee_Birthday" value="<?php echo htmlspecialchars($row['Employee_Birthday']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_Nationality" value="<?php echo htmlspecialchars($row['Employee_Nationality']); ?>" readonly class="view-only"></td>
                    <td><input type="number" name="Employee_Salary" value="<?php echo htmlspecialchars($row['Employee_Salary']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_Health_Insurance" value="<?php echo htmlspecialchars($row['Employee_Health_Insurance']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_PhoneNumber" value="<?php echo htmlspecialchars($row['Employee_PhoneNumber']); ?>" readonly class="view-only"></td>
                    <td><input type="text" name="Employee_Emergency_Contact_No" value="<?php echo htmlspecialchars($row['Employee_Emergency_Contact_No']); ?>" readonly class="view-only"></td>
                    <td>
                        <input type="hidden" name="Employee_ID" value="<?php echo htmlspecialchars($row['Employee_ID']); ?>">
                        <button type="button" class="editButton">Edit</button>
                        <button type="submit" name="save_employee" class="saveButton" style="display: none;">Save</button>
                        <button type="submit" name="delete_employee" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="14">No employees found.</td>
        </tr>
    <?php endif; ?>
</table>

<script>
    document.querySelectorAll('.editButton').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const inputs = row.querySelectorAll('.view-only');
            inputs.forEach(input => {
                input.removeAttribute('readonly');
                input.classList.add('editable');
            });
            row.querySelector('.saveButton').style.display = 'inline-block';
            this.style.display = 'none';
        });
    });
</script>
</body>
</html>
