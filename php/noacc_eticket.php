<?php
session_start();
include('db.php'); // Include your database connection

// Include Composer autoloader to load TCPDF
require_once __DIR__ . '/../vendor/autoload.php';

// Retrieve necessary data from session
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 1;
$passengers = $_SESSION['passengers'] ?? [];
$reservationID = $_SESSION['reservation_id'] ?? 'N/A';
$selectedAddonsForConfirmation = $_SESSION['selected_addons_for_confirmation'] ?? [];

// Set airline name
$airlineName = "AIR ANGEL";

// Check if flight information exists
if (!$selectedFlight) {
    die("Error: No flight information found.");
}

// Extract flight details
$flightNumber = $selectedFlight['Flight_Number'] ?? 'N/A';
$origin = $selectedFlight['Origin'] ?? 'N/A';
$destination = $selectedFlight['Destination'] ?? 'N/A';
$departureTime = $selectedFlight['Departure_Time'] ?? 'N/A';
$arrivalTime = $selectedFlight['Arrival_Time'] ?? 'N/A';

// Ensure the 'passengers' session data is set and has the required fields
if (isset($_SESSION['passengers']) && is_array($_SESSION['passengers'])) {
    // Prepare passenger names from session data
    $passengerNames = [];
    foreach ($_SESSION['passengers'] as $passenger) {
        // Ensure the required fields exist in each passenger data
        $fullName = $passenger['first_name'] . ' ' . 
                    ($passenger['middle_name'] ? $passenger['middle_name'] . ' ' : '') . 
                    $passenger['last_name'];

        // Add the full name to the array
        $passengerNames[] = $fullName;
    }

    // Print the passenger names (or use as needed)
} else {
    echo "No passenger data found in session.";
}

// If the form is submitted to download the ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new PDF document using TCPDF
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Set title and content
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

    // Add-ons
    if (!empty($selectedAddonsForConfirmation)) {
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Add-ons:", 0, 1);
        foreach ($selectedAddonsForConfirmation as $addon) {
            $pdf->Cell(0, 10, "{$addon['Name']} - \${$addon['Price']}", 0, 1);
        }
    }

    // Passenger Information
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Passenger(s):", 0, 1);
    foreach ($passengerNames as $index => $name) {
        $pdf->Cell(0, 10, ($index + 1) . ". $name", 0, 1);
    }

    // Set the path for saving the ticket file
    $ticketDir = $_SERVER['DOCUMENT_ROOT'] . '/ANGEL/etickets/';
    
    // Create the directory if it doesn't exist
    if (!is_dir($ticketDir)) {
        mkdir($ticketDir, 0777, true);
    }
    
    $ticketFile = $ticketDir . "eticket_$reservationID.pdf"; // Save the file to the 'etickets' folder

    // Save PDF to the file system
    $pdf->Output($ticketFile, 'F'); // Save the PDF to the file

    // Force download the generated PDF file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($ticketFile) . '"');
    header('Content-Length: ' . filesize($ticketFile));
    readfile($ticketFile);

    // Delete the file after download
    unlink($ticketFile);
    exit;
}
?>

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
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
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
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <form method="post" action="">
            <button type="submit">Print Ticket</button>
        </form>
    </div>
</body>
</html>
