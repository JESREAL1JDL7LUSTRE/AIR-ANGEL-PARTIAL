<?php
// Existing session and database setup
session_start();
include('db.php');
require_once __DIR__ . '/../vendor/autoload.php';

// Ensure user is logged in
if (!isset($_SESSION['Account_Email']) || empty($_SESSION['Account_Email'])) {
    header("Location: signin.php");
    exit;
}

// Retrieve session data
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 1;
$passengers = $_SESSION['passengers'] ?? [];
$reservationID = $_SESSION['reservation_id'] ?? 'N/A';
$selectedAddonsForConfirmation = $_SESSION['selected_addons_for_confirmation'] ?? [];

$airlineName = "AIR ANGEL";

// Check for missing flight information
if (!$selectedFlight) {
    die("Error: No flight information found.");
}

// Extract flight details
$flightNumber = $selectedFlight['Flight_Number'] ?? 'N/A';
$origin = $selectedFlight['Origin'] ?? 'N/A';
$destination = $selectedFlight['Destination'] ?? 'N/A';
$departureTime = $selectedFlight['Departure_Time'] ?? 'N/A';
$arrivalTime = $selectedFlight['Arrival_Time'] ?? 'N/A';
$flightPrice = $selectedFlight['Amount'] ?? 0;

// Initialize total to 0
$total = 0;

// Calculate total amount (flight + add-ons)
$totalAmount = ($flightPrice * $numPassengers); // Flight cost for all passengers

foreach ($selectedAddonsForConfirmation as $addon) {
    $addonPrice = $addon['Price'] ?? 0;
    $total += $addonPrice; // Add each add-on's price
}

$totalAmount += $total; 


// Prepare passenger names
$passengerNames = [];
if (isset($_SESSION['passengers']) && is_array($_SESSION['passengers'])) {
    foreach ($_SESSION['passengers'] as $passenger) {
        $firstName = $passenger['first_name'] ?? 'N/A';
        $middleName = $passenger['middle_name'] ?? '';
        $lastName = $passenger['last_name'] ?? 'N/A';
        $fullName = $firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName;
        $passengerNames[] = $fullName;
    }
} else {
    echo "No passenger data found in session.";
    exit;
}

// If the form is submitted to download the ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdf = new TCPDF();
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "$airlineName - E-Ticket", 0, 1, 'C');

    // Flight Information
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Flight Number: $flightNumber", 0, 1);
    $pdf->Cell(0, 10, "Origin: $origin", 0, 1);
    $pdf->Cell(0, 10, "Destination: $destination", 0, 1);
    $pdf->Cell(0, 10, "Departure Time: $departureTime", 0, 1);
    $pdf->Cell(0, 10, "Arrival Time: $arrivalTime", 0, 1);
    $pdf->Cell(0, 10, "Amount: $flightPrice per passenger", 0, 1);

    // Add-ons
    if (!empty($selectedAddonsForConfirmation)) {
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Add-ons:", 0, 1);
        foreach ($selectedAddonsForConfirmation as $addon) {
            $addonName = $addon['Name'] ?? 'N/A';
            $addonPrice = $addon['Price'] ?? 0;
            $pdf->Cell(0, 10, "$addonName - \$" . number_format($addonPrice, 2), 0, 1);
        }
    }

    // Passenger Information
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Passenger(s):", 0, 1);
    foreach ($passengerNames as $index => $name) {
        $pdf->Cell(0, 10, ($index + 1) . ". $name", 0, 1);
    }

    // Total Amount
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Total Amount: \$" . number_format($totalAmount, 2), 0, 1);

    // Save and download PDF
    $ticketDir = $_SERVER['DOCUMENT_ROOT'] . '/ANGEL/etickets/';
    if (!is_dir($ticketDir)) {
        mkdir($ticketDir, 0777, true);
    }
    
    $ticketFile = $ticketDir . "eticket_$reservationID.pdf";
    $pdf->Output($ticketFile, 'F');

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($ticketFile) . '"');
    header('Content-Length: ' . filesize($ticketFile));
    readfile($ticketFile);

    unlink($ticketFile);
    exit;
}
?>

<!-- HTML Code for Displaying Ticket -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .eticket {
            border: 2px solid #000;
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        .eticket-header {
            text-align: center;
        }
        .eticket-header h1 {
            margin: 0;
        }
        .eticket-details {
            margin-top: 20px;
        }
        .eticket-details p {
            margin: 5px 0;
        }
        .eticket-details strong {
            display: inline-block;
            width: 150px;
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
</header>

<div class="eticket">
    <div class="eticket-header">
        <h1><?php echo $airlineName; ?></h1>
        <p><strong>E-Ticket</strong></p>
    </div>

    <div class="eticket-details">
        <p><strong>Flight Number:</strong> <?php echo htmlspecialchars($flightNumber); ?></p>
        <p><strong>Origin:</strong> <?php echo htmlspecialchars($origin); ?></p>
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($destination); ?></p>
        <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($departureTime); ?></p>
        <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($arrivalTime); ?></p>
        <p><strong>Amount:</strong> $<?php echo number_format($selectedFlight['Amount'], 2); ?> per passenger</p>
    </div>

    <div class="eticket-details">
        <h3>Passenger(s)</h3>
        <?php foreach ($passengerNames as $index => $name): ?>
            <p><?php echo htmlspecialchars($index + 1 . ". " . $name); ?></p>
        <?php endforeach; ?>
    </div>

    <div class="eticket-details">
        <h3>Add-ons</h3>
        <?php if (!empty($selectedAddonsForConfirmation)): ?>
            <?php foreach ($selectedAddonsForConfirmation as $addon): ?>
                <p><?php echo htmlspecialchars($addon['Name']) . " - $" . htmlspecialchars($addon['Price']); ?></p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No add-ons selected.</p>
        <?php endif; ?>
    </div>

    <div class="eticket-details">
    <p><strong>Total Amount:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
</div>

<div style="text-align: center; margin-top: 20px;">
    <form method="post" action="">
        <button type="submit">Print Ticket</button>
    </form>
</div>
</body>
</html>
