<?php
session_start();
include('db.php'); // Include your database connection

// Retrieve data from session
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 0;
$selectedAddons = $_SESSION['selected_addons'] ?? [];
$paymentMethod = $_SESSION['payment_method'] ?? null;

// Generate booking date
$bookingDate = date('Y-m-d');

// Calculate total price
$totalPrice = 0;
$flightPrice = 0;

if ($selectedFlight) {
    $flightPrice = $selectedFlight['Amount'] * $numPassengers;
    $totalPrice += $flightPrice;
}

foreach ($selectedAddons as $addon) {
    $totalPrice += $addon['Price'];
}

// Insert reservation into the Reservation table
$reservationID = $_SESSION['reservation_id'] ?? null;

if (!$reservationID) {
    $reservationQuery = "INSERT INTO Reservation (Booking_date) VALUES ('$bookingDate')";
    if (mysqli_query($conn, $reservationQuery)) {
        $reservationID = mysqli_insert_id($conn); // Fetch the newly created reservation ID
        $_SESSION['reservation_id'] = $reservationID; // Save it in session
    } else {
        die("Database error: " . mysqli_error($conn));
    }
}

// Insert payment into Payment table, including Payment_Method_Name
$paymentID = null;
$paymentQuery = "INSERT INTO Payment (Payment_Amount, Payment_Date, Payment_Method_Name) 
                 VALUES ('$totalPrice', '$bookingDate', '$paymentMethod')";
if (mysqli_query($conn, $paymentQuery)) {
    $paymentID = mysqli_insert_id($conn);
} else {
    die("Database error (Payment): " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        table { width: 50%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid black; text-align: left; padding: 8px; }
    </style>
</head>
<body>
<h1>Booking Confirmation</h1>

<h2>Flight Information</h2>
<?php if ($selectedFlight): ?>
    <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($selectedFlight['Flight_Number'] ?? 'N/A'); ?></p>
    <p><strong>Departure Date:</strong> <?php echo htmlspecialchars($selectedFlight['Departure_Date'] ?? 'N/A'); ?></p>
    <p><strong>Origin:</strong> <?php echo htmlspecialchars($selectedFlight['Origin'] ?? 'N/A'); ?></p>
    <p><strong>Destination:</strong> <?php echo htmlspecialchars($selectedFlight['Destination'] ?? 'N/A'); ?></p>
    <p><strong>Amount:</strong> $<?php echo number_format($selectedFlight['Amount'], 2); ?> per passenger</p>
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
<p><strong>Flight Price (for <?php echo $numPassengers; ?> passengers):</strong> $<?php echo number_format($flightPrice, 2); ?></p>
<?php if ($totalPrice > $flightPrice): ?>
    <p><strong>Add-ons:</strong> $<?php echo number_format($totalPrice - $flightPrice, 2); ?></p>
<?php endif; ?>
<p><strong>Total:</strong> $<?php echo number_format($totalPrice, 2); ?> USD</p>

<h2>Payment Information</h2>
<p><strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentMethod); ?></p>
<p><strong>Payment ID:</strong> <?php echo htmlspecialchars($paymentID); ?></p>

<h2>Reservation Details</h2>
<p><strong>Reservation ID:</strong> <?php echo htmlspecialchars($reservationID); ?></p>
<p><strong>Booking Date:</strong> <?php echo htmlspecialchars($bookingDate); ?></p>

</body>
</html>
