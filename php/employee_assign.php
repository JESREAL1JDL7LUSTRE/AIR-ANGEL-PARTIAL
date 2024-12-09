<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Is_Admin']) || $_SESSION['Is_Admin'] !== 1) {
    header('Location: signin.php'); // Redirect to login page if not an admin
    exit;
}

// Handle form submission to update user info
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Employee_Assignment_ID'])) {
        // Get the updated data from the form
        $Employee_Role = $_POST['Employee_Role'];
        $Employee_ID_FK = $_POST['Employee_ID_FK'];
        $Available_Flights_Number_ID_FK = $_POST['Available_Flights_Number_ID_FK'];
        $Employee_Assignment_ID = $_POST['Employee_Assignment_ID']; // Ensure you get the ID to update

        // Prepare the SQL query to update the account info
        $sql_update = "UPDATE Employee_assignment 
                        SET Employee_Role = ?, 
                            Employee_ID_FK = ?, 
                            Available_Flights_Number_ID_FK = ? 
                        WHERE Employee_Assignment_ID = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("siii", $Employee_Role, $Employee_ID_FK, $Available_Flights_Number_ID_FK, $Employee_Assignment_ID);

        // Execute the update query
        if ($stmt->execute()) {
            echo "<script>alert('Updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating!');</script>";
        }
    }

    // Add new employee assignment
    if (isset($_POST['Assign_employee'])) {
        $Employee_Role = $_POST['Employee_Role'];
        $Employee_ID_FK = $_POST['Employee_ID_FK'];
        $Available_Flights_Number_ID_FK = $_POST['Available_Flights_Number_ID_FK'];

        // Insert into Employee_assignment
        $sql_insert = "INSERT INTO Employee_assignment (Employee_Role, Employee_ID_FK, Available_Flights_Number_ID_FK) 
                      VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("sii", $Employee_Role, $Employee_ID_FK, $Available_Flights_Number_ID_FK);

        if ($stmt->execute()) {
            echo "<script>alert('Employee assigned successfully!');</script>";
        } else {
            echo "<script>alert('Error assigning employee!');</script>";
        }
    }
}

// Fetch employee assignment data from the database
$sql_employee_assign = "
SELECT 
    ea.Employee_Assignment_ID,
    ea.Employee_Role,
    ea.Employee_ID_FK,
    ea.Available_Flights_Number_ID_FK,
    e.Employee_Last_Name,
    e.Employee_First_Name,
    e.Employee_Middle_Name,
    e.Department,
    f.Flight_Number,
    f.Origin,
    f.Destination
FROM 
    Employee_assignment ea
JOIN 
    employees e ON ea.Employee_ID_FK = e.Employee_ID
JOIN 
    available_flights f ON ea.Available_Flights_Number_ID_FK = f.Available_Flights_Number_ID;
";
$stmt = $conn->prepare($sql_employee_assign);
$stmt->execute();
$assign_result = $stmt->get_result();

// Search functionality
$searchQuery = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = '%' . $conn->real_escape_string(trim($_GET['search'])) . '%';
    $searchQuery = "WHERE e.Employee_First_Name LIKE '$search' OR 
                           e.Employee_Last_Name LIKE '$search' OR 
                           e.Employee_Email LIKE '$search' OR 
                           e.Department LIKE '$search' OR 
                           ea.Employee_Role LIKE '$search' OR
                           f.Flight_Number LIKE '$search' OR
                           f.Origin LIKE '$search' OR
                           f.Destination LIKE '$search'";
}

$sql = "SELECT ea.Employee_Assignment_ID, ea.Employee_Role, ea.Employee_ID_FK, ea.Available_Flights_Number_ID_FK, 
                e.Employee_Last_Name, e.Employee_First_Name, e.Employee_Middle_Name, e.Department, 
                f.Flight_Number, f.Origin, f.Destination
        FROM Employee_assignment ea
        JOIN employees e ON ea.Employee_ID_FK = e.Employee_ID
        JOIN available_flights f ON ea.Available_Flights_Number_ID_FK = f.Available_Flights_Number_ID
        $searchQuery";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Employee Assignment Management</title>
    <link rel="stylesheet" href="/ANGEL/styles/signup.css">
    <script>
        // Show or hide the admin signup form
        function toggleAssignForm() {
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
                <li><a href="admin.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <h1>Welcome Admin!</h1>

    <h2>Employee Assignment Management</h2>
    <button onclick="toggleAssignForm()">Assign Employee</button>

    <!-- Add Employee Form -->
    <div id="adminSignupForm" style="display:none;">
        <h2>Assign Employee</h2>
        <form method="POST">
            <input type="text" name="Employee_Role" placeholder="Employee_Role" required>
            <input type="text" name="Employee_ID_FK" placeholder="Employee_ID_FK" required>
            <input type="text" name="Available_Flights_Number_ID_FK" placeholder="Available_Flights_Number_ID_FK">
            <button type="submit" name="Assign_employee">Add</button>
        </form>
    </div>

    <h2>All Employees</h2>

    <!-- Search Bar -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

    <table border="1">
        <tr>
            <th>Employee_Assignment_ID</th>
            <th>Employee_Role</th>
            <th>Employee_ID_FK</th>
            <th>Available_Flights_Number_ID_FK</th>
            <th>Employee_Last_Name</th>
            <th>Employee_First_Name</th>
            <th>Employee_Middle_Name</th>
            <th>Department</th>
            <th>Flight_Number</th>
            <th>Origin</th>
            <th>Destination</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Employee_Assignment_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Role']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_ID_FK']); ?></td>
                    <td><?php echo htmlspecialchars($row['Available_Flights_Number_ID_FK']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Last_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_First_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Employee_Middle_Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Department']); ?></td>
                    <td><?php echo htmlspecialchars($row['Flight_Number']); ?></td>
                    <td><?php echo htmlspecialchars($row['Origin']); ?></td>
                    <td><?php echo htmlspecialchars($row['Destination']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="14">No employees found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?> 
