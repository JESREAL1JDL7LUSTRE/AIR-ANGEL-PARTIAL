<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch seat selector items from the database
$sql = "SELECT Seat_Selector_ID, Seat_Selector_Number, Price FROM Seat_Selector";
$result = $conn->query($sql);

// Fetch seat selectors that are already in the 'add_on' table (i.e., already selected by the user)
$query = "SELECT DISTINCT Seat_Selector_ID_FK FROM add_on WHERE Seat_Selector_ID_FK IS NOT NULL";
$existingAddonsResult = $conn->query($query);
$existingAddons = [];

if ($existingAddonsResult->num_rows > 0) {
    while ($row = $existingAddonsResult->fetch_assoc()) {
        $existingAddons[] = $row['Seat_Selector_ID_FK'];
    }
}

$selectedAddons = isset($_SESSION['selected_addons']) ? $_SESSION['selected_addons'] : [];

// Only show seat selectors that are not already in the 'add_on' table
$availableSeatSelectors = [];
while ($row = $result->fetch_assoc()) {
    if (!in_array($row['Seat_Selector_ID'], $existingAddons)) {
        $availableSeatSelectors[] = $row; // Add the seat selector if it's not already in the add_on table
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $addon_id = $_POST['addon_id'];

    // Add selected add-on to session if it's not already in the 'add_on' table
    if (!in_array($addon_id, $existingAddons)) {
        // Add selected add-on to session
        $addon = [
            'ID' => $addon_id,
            'Name' => $_POST['addon_name'],
            'Price' => $_POST['addon_price'],
            'Type' => 'SeatSelector' // Set the add-on type as SeatSelector
        ];

        // Append the selected add-on to session array
        $selectedAddons[] = $addon;

        // Save the selected add-ons back to session
        $_SESSION['selected_addons'] = $selectedAddons;

    } else {
        echo "This seat selector has already been selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seat Selector Add-ons</title>
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

<?php if (count($availableSeatSelectors) > 0): ?>
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
                <form method="POST" action="acc_addons.php">
                    <input type="hidden" name="addon_id" value="<?php echo $row['Seat_Selector_ID']; ?>">
                    <input type="hidden" name="addon_name" value="<?php echo $row['Seat_Selector_Number']; ?>">
                    <input type="hidden" name="addon_price" value="<?php echo $row['Price']; ?>">
                    <input type="hidden" name="addon_type" value="SeatSelector">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No seat selectors available.</p>
<?php endif; ?>

<br>
<button onclick="window.location.href='acc_addons.php'">View All Add-ons</button>

</body>
</html>
