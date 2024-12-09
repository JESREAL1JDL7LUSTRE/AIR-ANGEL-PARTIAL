<?php
session_start();
include('db.php'); // Include your database connection
// Check if flight ID exists in the session
if (!isset($_SESSION['selected_flight_id'])) {
    echo "Error: No flight selected. Please go back and choose a flight.";
    exit;
}

$selectedFlightID = $_SESSION['selected_flight_id'];

// Optionally, fetch full flight details from the database
$stmt = $conn->prepare("SELECT * FROM Available_Flights WHERE Available_Flights_Number_ID = ?");
$stmt->bind_param("i", $selectedFlightID);
$stmt->execute();
$result = $stmt->get_result();
$selectedFlight = $result->fetch_assoc();

if ($selectedFlight) {
    // Use $selectedFlight data as needed
    $_SESSION['selected_flight'] = $selectedFlight; // Store full details if needed
} else {
    echo "Error: Selected flight not found in the database.";
    exit;
}
// Ensure that the passengers' data exists in the session
if (isset($_SESSION['passengers']) && !empty($_SESSION['passengers'])) {
    $passengers = $_SESSION['passengers']; // Get the passengers data from the session
} else {
    die("No passenger data found. Please go back and enter passenger information.");
}

// Ensure the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Initialize selected_addons if it's not set
if (!isset($_SESSION['selected_addons'])) {
    $_SESSION['selected_addons'] = [];
}

// Retrieve selected add-ons from session
$selectedAddons = $_SESSION['selected_addons'];

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_addon'])) {
    if (isset($_POST['unique_id']) && !empty($_POST['unique_id'])) {
        $uniqueIdToDelete = $_POST['unique_id'];

        // Delete the specific add-on by unique ID
        foreach ($selectedAddons as $key => $addon) {
            if (isset($addon['unique_id']) && $addon['unique_id'] === $uniqueIdToDelete) {
                unset($selectedAddons[$key]);
                break;
            }
        }

        // Reindex the array after deletion and save it back to the session
        $_SESSION['selected_addons'] = array_values($selectedAddons);
    } else {
        echo "<p>Error: Unique ID is missing!</p>";
    }
}

// Handle add-to-cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Add selected add-on to session with a unique ID
    $addon = [
        'unique_id' => uniqid('addon_', true), // Generate a unique ID for each add-on
        'ID' => $_POST['addon_id'],
        'Name' => $_POST['addon_name'],
        'Price' => $_POST['addon_price'],
        'Type' => $_POST['addon_type']
    ];

    // Append the selected add-on to session array
    $selectedAddons[] = $addon;

    // Save the selected add-ons back to session
    $_SESSION['selected_addons'] = $selectedAddons;
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    // Only store the names of all add-ons for the payment confirmation page
    $selectedAddonsForConfirmation = [];
    foreach ($selectedAddons as $addon) {
        $selectedAddonsForConfirmation[] = [
            'Name' => $addon['Name'],
            'Price' => $addon['Price']
        ];
    }

    // Save the selected add-ons' names for confirmation in session
    $_SESSION['selected_addons_for_confirmation'] = $selectedAddonsForConfirmation;

    // Redirect to payment.php with the session data
    header("Location: acc_payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Add-ons</title>
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
    <script>
        // Go back to the previous page
        function goBack() {
            window.history.back();
        }
    </script>
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
<h1>Selected Add-ons</h1>

<?php if (!empty($selectedAddons)): ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($selectedAddons as $addon): ?>
            <tr>
                <td><?php echo htmlspecialchars($addon['Name']); ?></td>
                <td><?php echo htmlspecialchars($addon['Price']); ?></td>
                <td><?php echo htmlspecialchars($addon['Type']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="unique_id" value="<?php echo htmlspecialchars($addon['unique_id']); ?>">
                        <button type="submit" name="delete_addon">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No add-ons selected.</p>
<?php endif; ?>

<br>
<button onclick="window.location.href='acc_addons_baggage.php'">Add Baggage</button>
<button onclick="window.location.href='acc_addons_food.php'">Add Food</button>
<button onclick="window.location.href='acc_addons_seat_selector.php'">Add Seat</button>

<br><br>
<!-- This button will now take the user to payment.php with add-ons info in the session -->
<form method="POST">
    <button type="submit" name="proceed_to_payment">Proceed to Payment</button>
    <button type="button" onclick="goBack()">Go Back</button>
</form>

</body>
</html>
