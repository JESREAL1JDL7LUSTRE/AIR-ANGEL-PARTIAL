<?php
session_start();
include('db.php'); // Include your database connection

// Ensure the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

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

$selectedReturnFlight = $_SESSION['return_flights'] ?? null;

// Check if it's an array and extract the first element
if (is_array($selectedReturnFlight) && isset($selectedReturnFlight[0])) {
    $selectedReturnFlight = $selectedReturnFlight[0]; // Extract the first flight data
} else {
    $selectedReturnFlight = null; // No valid data available
}


// Generate booking date
$bookingDate = date('Y-m-d');

// Calculate total price
$totalPrice = 0;
$departureFlightPrice = 0;
$returnFlightPrice = 0;
$addonTotal = 0;

if ($selectedFlight) {
    $departureFlightPrice = $selectedFlight['Amount'] * $numPassengers;
    $totalPrice += $departureFlightPrice;
}

if ($selectedReturnFlight) {
    $returnFlightPrice = $selectedReturnFlight['Amount'] * $numPassengers;
    $totalPrice += $returnFlightPrice;
}

// Add selected add-ons to the total price
foreach ($selectedAddons as $addon) {
    if ($selectedReturnFlight) {
        $addonTotal += $addon['Price'] * 2;
    }else {
        $addonTotal += $addon['Price'];  // Add the price of each selected addon
    }
    

}


// Add add-ons to the total
$totalPrice += $addonTotal;
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
    <p>No departure flight selected.</p>
<?php endif; ?>

<?php if ($selectedReturnFlight): ?>
    <h3>Return Flight Information</h3>
    <p>Flight Number: <?php echo htmlspecialchars($selectedReturnFlight['Flight_Number'] ?? 'N/A'); ?></p>
    <p>Return Date: <?php echo htmlspecialchars($selectedReturnFlight['Departure_Date'] ?? 'N/A'); ?></p>
    <p>Origin: <?php echo htmlspecialchars($selectedReturnFlight['Origin'] ?? 'N/A'); ?></p>
    <p>Destination: <?php echo htmlspecialchars($selectedReturnFlight['Destination'] ?? 'N/A'); ?></p>
    <p>Amount: $<?php echo number_format($selectedReturnFlight['Amount'] ?? 0, 2); ?> per passenger</p>
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
<p><strong>Flight Price (for <?php echo $numPassengers; ?> passengers):</strong> $<?php echo number_format($departureFlightPrice, 2); ?></p>
<?php if ($returnFlightPrice > 0): ?>
    <p><strong>Return Flight Price (for <?php echo $numPassengers; ?> passengers):</strong> $<?php echo number_format($returnFlightPrice, 2); ?></p>
<?php endif; ?>

<?php if ($addonTotal > 0): ?>
    <p><strong>Add-ons:</strong> $<?php echo number_format($addonTotal, 2); ?></p>
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

<button type="button" onclick="window.location.href='acc_eticket.php'">Print E-ticket</button>
</body>
</html>
