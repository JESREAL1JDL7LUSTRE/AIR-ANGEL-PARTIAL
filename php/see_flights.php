<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Is_Admin']) || $_SESSION['Is_Admin'] !== 1) {
    header('Location: signin.php'); // Redirect to login page if not an admin
    exit;
}

// Handle flight updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['Account_Email'])) {
        // Handle update logic here (as in your code)
    }
}

// Handle flight deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Handle delete logic here (as in your code)
}

// Fetch flights with optional search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

$sql = "
    SELECT 
        Available_Flights_Number_ID, 
        Flight_Number, 
        Departure_Date, 
        Arrival_Date, 
        Origin, 
        Destination, 
        Departure_Time, 
        Arrival_Time, 
        Amount 
    FROM Available_Flights
    WHERE 
        Flight_Number LIKE ? OR 
        Departure_Date LIKE ? OR 
        Arrival_Date LIKE ? OR 
        Origin LIKE ? OR 
        Destination LIKE ?
";

$stmt = $conn->prepare($sql);
$search_term = '%' . $search_query . '%';
$stmt->bind_param('sssss', $search_term, $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <h1>Welcome Admin!</h1>
    <h2>All Available Flights</h2>

    <!-- Search Bar -->
    <form method="GET" action="">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by Flight Number, Origin, Destination..." 
            value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>

    <table border="1">
        <tr>
            <th>Available_Flights_Number_ID</th>
            <th>Flight_Number</th>
            <th>Departure_Date</th>
            <th>Arrival_Date</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Departure_Time</th>
            <th>Arrival_Time</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="">
                        <td><?php echo htmlspecialchars($row['Available_Flights_Number_ID']); ?></td>
                        <td><input type="text" name="Flight_Number" value="<?php echo htmlspecialchars($row['Flight_Number']); ?>" required class="view-only" readonly></td>
                        <td><input type="date" name="Departure_Date" value="<?php echo htmlspecialchars($row['Departure_Date']); ?>" required class="view-only" readonly></td>
                        <td><input type="date" name="Arrival_Date" value="<?php echo htmlspecialchars($row['Arrival_Date']); ?>" class="view-only" readonly></td>
                        <td><input type="text" name="Origin" value="<?php echo htmlspecialchars($row['Origin']); ?>" required class="view-only" readonly></td>
                        <td><input type="text" name="Destination" value="<?php echo htmlspecialchars($row['Destination']); ?>" class="view-only" readonly></td>
                        <td><input type="time" name="Departure_Time" value="<?php echo htmlspecialchars($row['Departure_Time']); ?>" required class="view-only" readonly></td>
                        <td><input type="time" name="Arrival_Time" value="<?php echo htmlspecialchars($row['Arrival_Time']); ?>" class="view-only" readonly></td>
                        <td><input type="text" name="Amount" value="<?php echo htmlspecialchars($row['Amount']); ?>" class="view-only" readonly></td>
                        <td>
                            <input type="hidden" name="Available_Flights_Number_ID" value="<?php echo $row['Available_Flights_Number_ID']; ?>">
                            <button type="button" class="editButton">Edit</button>
                            <button type="submit" class="saveButton" style="display: none;">Save</button>
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this flight?')">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">No flights found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script>
        const editButtons = document.querySelectorAll('.editButton');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
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
