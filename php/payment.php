<?php
session_start(); 

// Fetch the flight, passenger info, and add-ons from session
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 0;
$selectedAddons = $_SESSION['selected_addons'] ?? [];

// Debugging: Print the contents of selected_flight to check the structure
echo "<pre>";
print_r($selectedFlight);
echo "</pre>";

// Error message if no flight selected
if (!$selectedFlight) {
    echo "Error: No flight selected. Please choose a flight first.";
    exit(); 
}

// Initialize total price
$totalPrice = 0;

// Calculate total price
if ($selectedFlight) {
    // Assuming 'Amount' is the price of the selected flight
    $totalPrice += $selectedFlight['Amount'] * $numPassengers;
}

// Add price for each selected addon
foreach ($selectedAddons as $addon) {
    $totalPrice += $addon['Price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary</title>
</head>
<body>

<h1>Payment Summary</h1>

<h2>Flight Information</h2>
<?php if ($selectedFlight): ?>
    <p>Flight Number: <?php echo htmlspecialchars($selectedFlight['FlightNumber'] ?? 'N/A'); ?></p>
    <p>Departure Date: <?php echo htmlspecialchars($selectedFlight['DepartureDate'] ?? 'N/A'); ?></p>
    <p>Origin: <?php echo htmlspecialchars($selectedFlight['Origin'] ?? 'N/A'); ?></p>
    <p>Destination: <?php echo htmlspecialchars($selectedFlight['Destination'] ?? 'N/A'); ?></p>
    <p>Amount: $<?php echo number_format($selectedFlight['Amount'], 2); ?></p>
<?php else: ?>
    <p>No flight selected.</p>
<?php endif; ?>

<h2>Selected Add-ons</h2>
<?php if (!empty($selectedAddons)): ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Price</th>
        </tr>
        <?php foreach ($selectedAddons as $addon): ?>
            <tr>
                <td><?php echo htmlspecialchars($addon['Name']); ?></td>
                <td>$<?php echo number_format($addon['Price'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No add-ons selected.</p>
<?php endif; ?>

<h2>Total Price: $<?php echo number_format($totalPrice, 2); ?> USD</h2>

<h2>Payment Methods</h2>
<!-- Add payment methods or form here -->

</body>
</html>
