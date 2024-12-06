<?php
session_start();

// Initialize selected add-ons if not already set
if (!isset($_SESSION['selected_addons'])) {
    $_SESSION['selected_addons'] = [];
}

// Retrieve selected add-ons from session
$selectedAddons = $_SESSION['selected_addons'];

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_addon'])) {
    // Check if the 'unique_id' exists in the POST request before accessing it
    if (isset($_POST['unique_id']) && !empty($_POST['unique_id'])) {
        $uniqueIdToDelete = $_POST['unique_id'];

        // Delete the specific add-on by unique ID
        foreach ($selectedAddons as $key => $addon) {
            if (isset($addon['unique_id']) && $addon['unique_id'] === $uniqueIdToDelete) {
                unset($selectedAddons[$key]);  // Remove the specific add-on from the array
                break;  // Exit the loop once the item is found and deleted
            }
        }

        // Reindex the array after deletion and save it back to the session
        $_SESSION['selected_addons'] = array_values($selectedAddons);
    } else {
        // Optional: Add an error message if unique_id is missing
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
        'Type' => $_POST['addon_type'] // Dynamically set the add-on type
    ];

    // Append the selected add-on to session array
    $selectedAddons[] = $addon;

    // Save the selected add-ons back to session
    $_SESSION['selected_addons'] = $selectedAddons;
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    // Redirect to payment.php, carrying over the session data
    header("Location: payment.php");
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
</head>
<body>

<h1>Selected Add-ons</h1>

<?php if (!empty($selectedAddons)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($selectedAddons as $addon): ?>
            <?php if (isset($addon['unique_id']) && !empty($addon['unique_id'])): // Ensure valid add-on ?>
                <tr>
                    <td><?php echo htmlspecialchars($addon['ID']); ?></td>
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
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No add-ons selected.</p>
<?php endif; ?>

<br>
<button onclick="window.location.href='add_on_baggage.php'">Add Baggage</button>
<button onclick="window.location.href='add_on_food.php'">Add Food</button>
<button onclick="window.location.href='add_on_seat_selector.php'">Add Seat</button>

<br><br>
<!-- This button will now take the user to payment.php with add-ons info in the session -->
<form method="POST">
    <button type="submit" name="proceed_to_payment">Proceed to Payment</button>
</form>

</body>
</html>
