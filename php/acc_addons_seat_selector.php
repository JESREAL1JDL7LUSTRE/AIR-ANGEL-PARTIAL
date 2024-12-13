<?php
session_start();
include 'db.php'; // Database connection

// Get selected flight from session or POST
$selectedFlightID = $_SESSION['selected_flight_id'] ?? $_POST['selected_flight_id'] ?? null;

if (!$selectedFlightID) {
    die("Error: No flight selected.");
}

// Prepare the query to fetch available seats
$stmt3 = $conn->prepare("
SELECT Seat_Selector_ID, Seat_Selector_Number, Price
FROM Seat_Selector
WHERE Seat_Selector_ID NOT IN (
    SELECT COALESCE(Seat_Selector_ID_FK, 0)
    FROM add_on
    WHERE FRP_Number_ID_FK IN (
        SELECT FRP_Number_ID
        FROM flight_to_reservation_to_passenger
        WHERE Available_Flights_Number_ID_FK = ?
    )
    )
");

// Bind the selected flight ID to the query
$stmt3->bind_param("i", $selectedFlightID);
$stmt3->execute();

// Fetch results
$result = $stmt3->get_result();
$availableSeatSelectors = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt3->close();

// Handle POST request to add add-ons to the session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Initialize the session array if not already set
    $selectedAddons = $_SESSION['selected_addons'] ?? [];
    
    // Add the new add-on to the array
    $addon = [
        'ID' => $_POST['addon_id'],
        'Name' => $_POST['addon_name'],
        'Price' => $_POST['addon_price'],
        'Type' => 'Seat Selector' // Set the add-on type as Seat Selector
    ];
    $selectedAddons[] = $addon;

    // Save the updated add-ons back to the session
    $_SESSION['selected_addons'] = $selectedAddons;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Food Add-ons</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
</head>
<body>
<header>
        <div class="header-container">
                <h1 class="site-title">AirAngel - Airline Reservation</h1>
            </div>
            <nav>
                <ul>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="acc_account.php">Account</a></li>
                        <li><a href="acc_dashboard.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
<h1>Select Seat Selector Add-ons</h1>

<?php if (!empty($availableSeatSelectors)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Seat Selector Number</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($availableSeatSelectors as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Seat_Selector_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Seat_Selector_Number']); ?></td>
                <td><?php echo htmlspecialchars($row['Price']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="addon_id" value="<?php echo htmlspecialchars($row['Seat_Selector_ID']); ?>">
                        <input type="hidden" name="addon_name" value="<?php echo htmlspecialchars($row['Seat_Selector_Number']); ?>">
                        <input type="hidden" name="addon_price" value="<?php echo htmlspecialchars($row['Price']); ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No unselected seats are available for this flight.</p>
<?php endif; ?>

<!-- Button to redirect to add-ons overview -->
<button onclick="window.location.href='acc_addons.php'">View All Add-ons</button>

</body>
</html>
