<?php
ob_start();  // Start output buffering
session_start();  // Start the session
include('db.php'); // Include your database connection

// Initialize selected add-ons if not already set
if (!isset($_SESSION['selected_addons'])) {
    $_SESSION['selected_addons'] = [];
}

// Retrieve selected add-ons from session
$selectedAddons = $_SESSION['selected_addons'];

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_addon'])) {
    if (!empty($_POST['unique_id'])) {
        $uniqueIdToDelete = $_POST['unique_id'];

        foreach ($selectedAddons as $key => $addon) {
            if ($addon['unique_id'] === $uniqueIdToDelete) {
                unset($selectedAddons[$key]);
                break;
            }
        }

        $_SESSION['selected_addons'] = array_values($selectedAddons); // Reindex array
    }
}

// Handle add-to-cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $addon = [
        'unique_id' => uniqid('addon_', true),
        'ID' => $_POST['addon_id'],
        'Name' => $_POST['addon_name'],
        'Price' => $_POST['addon_price'],
        'Type' => $_POST['addon_type']
    ];

    $selectedAddons[] = $addon;
    $_SESSION['selected_addons'] = $selectedAddons;
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
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
