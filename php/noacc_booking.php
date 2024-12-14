<?php
session_start();
include('db.php'); // Include your database connection

// Assuming that Reservation_ID and Payment_ID are stored in session or can be retrieved from URL
$reservationID = $_SESSION['reservation_id'] ?? null; // Get reservation ID from session
$paymentID = $_SESSION['payment_id'] ?? null; // Get payment ID from session

// Retrieve reservation and payment data from the database
$reservationQuery = "SELECT * FROM Reservation WHERE Reservation_ID = ?";
$paymentQuery = "SELECT * FROM Payment WHERE Payment_ID = ?";

// Prepare and execute the queries
$reservationStmt = $conn->prepare($reservationQuery);
$reservationStmt->bind_param("i", $reservationID);
$reservationStmt->execute();
$reservationResult = $reservationStmt->get_result();
$reservation = $reservationResult->fetch_assoc();

$paymentStmt = $conn->prepare($paymentQuery);
$paymentStmt->bind_param("i", $paymentID);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
$payment = $paymentResult->fetch_assoc();

// If no data found for the reservation or payment
if (!$reservation || !$payment) {
    echo "Error: Reservation or payment information not found.";
    exit;
}

// Flight and passenger details can be fetched from the session as before
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
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
    <link rel="stylesheet" href="/ANGEL/styles/LAYOUT.css"> <!-- base (layout) -->
</head>
<body>
<header>
        <div class="header-container">
                <h1 class="site-title">AirAngel - Airline Reservation</h1>
            </div>
            <nav>
                <ul>
                <li><a href="signin.php">Sign In</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="noacc_dashboard.php">Home</a></li>
                </ul>
            </nav>
        </div>
</header>
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
<p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['Payment_Method_Name'] ?? 'N/A'); ?></p>
<p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment['Payment_ID'] ?? 'N/A'); ?></p>
<p><strong>Amount:</strong> $<?php echo number_format($payment['Payment_Amount'], 2); ?></p>
<p><strong>Payment Date:</strong> <?php echo htmlspecialchars($payment['Payment_Date'] ?? 'N/A'); ?></p>

<h2>Reservation Details</h2>
<p><strong>Reservation ID:</strong> <?php echo htmlspecialchars($reservation['Reservation_ID'] ?? 'N/A'); ?></p>
<p><strong>Booking Date:</strong> <?php echo htmlspecialchars($reservation['Booking_date'] ?? 'N/A'); ?></p>

</body>
<button type="button" onclick="window.location.href='noacc_eticket.php'">Print E-ticket</button>
</html>
