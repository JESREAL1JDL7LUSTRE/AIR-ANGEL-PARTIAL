<?php
session_start(); 

// Fetch the flight, passenger info, and add-ons from session
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 0;
$selectedAddons = $_SESSION['selected_addons'] ?? [];

// Calculate total price
$totalPrice = 0;
$flightPrice = 0;

if ($selectedFlight) {
    // Multiply the flight price by the number of passengers
    $flightPrice = $selectedFlight['Amount'] * $numPassengers;
    $totalPrice += $flightPrice;
}

// Add price for each selected addon
foreach ($selectedAddons as $addon) {
    $totalPrice += $addon['Price'];
}

// Handle form submission for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? null;

    if (!$paymentMethod) {
        echo "Error: Please select a payment method.";
        exit;
    }

    // Save the selected payment method to the session
    $_SESSION['payment_method'] = $paymentMethod;

    // Redirect to the confirmation page
    header("Location: confirm_booking.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary</title>
    <script>
        function toggleCashPay() {
            document.getElementById('cash_payment').style.display = 'block';
            document.getElementById('ecash_payment').style.display = 'none';
            document.getElementById('card_payment').style.display = 'none';
        }

        function toggleEcashPay() {
            document.getElementById('ecash_payment').style.display = 'block';
            document.getElementById('cash_payment').style.display = 'none';
            document.getElementById('card_payment').style.display = 'none';
        }

        function toggleCardPay() {
            document.getElementById('card_payment').style.display = 'block';
            document.getElementById('cash_payment').style.display = 'none';
            document.getElementById('ecash_payment').style.display = 'none';
        }
    </script>
</head>
<body>

<h1>Payment Summary</h1>

<h2>Flight Information</h2>
<?php if ($selectedFlight): ?>
    <p>Flight Number: <?php echo htmlspecialchars($selectedFlight['Flight_Number'] ?? 'N/A'); ?></p>
    <p>Departure Date: <?php echo htmlspecialchars($selectedFlight['Departure_Date'] ?? 'N/A'); ?></p>
    <p>Origin: <?php echo htmlspecialchars($selectedFlight['Origin'] ?? 'N/A'); ?></p>
    <p>Destination: <?php echo htmlspecialchars($selectedFlight['Destination'] ?? 'N/A'); ?></p>
    <p>Amount: $<?php echo number_format($selectedFlight['Amount'], 2); ?> per passenger</p>
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

<h2>Total Price</h2>
<p>Flight Price (for <?php echo $numPassengers; ?> passengers): $<?php echo number_format($flightPrice, 2); ?></p>
<?php if ($totalPrice > $flightPrice): ?>
    <p>Add-ons: $<?php echo number_format($totalPrice - $flightPrice, 2); ?></p>
<?php endif; ?>
<h3>Total: $<?php echo number_format($totalPrice, 2); ?> USD</h3>

<h2>Payment Methods</h2>
<form method="POST">
    <label><input type="radio" name="payment_method" value="Cash" onclick="toggleCashPay()" required> Cash</label><br>
    <label><input type="radio" name="payment_method" value="ECash" onclick="toggleEcashPay()"> ECash</label><br>
    <label><input type="radio" name="payment_method" value="Card" onclick="toggleCardPay()"> Card</label><br>

    <div id="cash_payment" style="display: none;">
        <h3>Pay at a branch</h3>
        <p>Take the flight details and pay at a branch. Your reference ID is: 12345.</p>
    </div>
    <div id="ecash_payment" style="display: none;">
        <h3>Here is the account number: 0963874825383</h3>
    </div>
    <div id="card_payment" style="display: none;">
        <h3>Enter Card Details</h3>
        <label>Card Number: <input type="text" name="card_number"></label><br>
        <label>Expiry Date: <input type="month" name="expiry_date"></label><br>
        <label>CVV: <input type="text" name="cvv"></label>
    </div>

    <button type="submit">Submit Payment</button>
</form>

</body>
</html>
