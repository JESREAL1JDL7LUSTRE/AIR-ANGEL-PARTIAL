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
$selectedFlight = $_SESSION['selected_flight'] ?? null; // Departure flight
$selectedReturnFlight = $_SESSION['selected_return_flight'] ?? null; // Return flight
$numPassengers = $_SESSION['num_passengers'] ?? 1;
$passengers = $_SESSION['passengers'] ?? [];
$reservationID = $_SESSION['reservation_id'] ?? 'N/A';
$selectedAddonsForConfirmation = $_SESSION['selected_addons_for_confirmation'] ?? [];

$airlineName = "AIR ANGEL";

// Ensure departure flight information is available
if (!$selectedFlight) {
    die("Error: No departure flight information found.");
}

// Extract departure flight details
$flightNumber = $selectedFlight['Flight_Number'] ?? 'N/A';
$origin = $selectedFlight['Origin'] ?? 'N/A';
$destination = $selectedFlight['Destination'] ?? 'N/A';
$departureTime = $selectedFlight['Departure_Time'] ?? 'N/A';
$arrivalTime = $selectedFlight['Arrival_Time'] ?? 'N/A';
$flightPrice = $selectedFlight['Amount'] ?? 0;

// Retrieve the return flights data from the session
$selectedReturnFlight = $_SESSION['return_flights'] ?? null;

// Check if it's an array and extract the first element
if (is_array($selectedReturnFlight) && isset($selectedReturnFlight[0])) {
    $selectedReturnFlight = $selectedReturnFlight[0]; // Extract the first flight data
} else {
    $selectedReturnFlight = null; // No valid data available
}

// Ensure return flight details are available (if applicable)
$returnFlightNumber = $selectedReturnFlight['Flight_Number'] ?? 'N/A';
$returnOrigin = $selectedReturnFlight['Origin'] ?? 'N/A';
$returnDestination = $selectedReturnFlight['Destination'] ?? 'N/A';
$returnDepartureTime = $selectedReturnFlight['Departure_Time'] ?? 'N/A';
$returnArrivalTime = $selectedReturnFlight['Arrival_Time'] ?? 'N/A';
$returnFlightPrice = $selectedReturnFlight['Amount'] ?? 0;

$numPassengers = $_SESSION['num_passengers'] ?? 0;

// Initialize total to 0
$total = 0;

// Calculate total amount (flight + add-ons)
$totalAmount = ($flightPrice * $numPassengers); // Flight cost for all passengers
$totalAmountReturn = ($returnFlightPrice * $numPassengers); // Return flight cost

foreach ($selectedAddonsForConfirmation as $addon) {
    $addonPrice = $addon['Price'] ?? 0;
    $total += $addonPrice * $numPassengers; // Add each add-on's price
}

$totalAmount += $total; // Add addons cost for the departure flight
$totalAmountReturn += $total; // Add addons cost for the return flight

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

// If the form is submitted for the departure flight ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_departure_ticket'])) {
    // Generate Departure Ticket
    $pdfDeparture = new TCPDF();
    $pdfDeparture->AddPage();
    $pdfDeparture->SetFont('helvetica', 'B', 16);
    $pdfDeparture->Cell(0, 10, "$airlineName - E-Ticket (Departure)", 0, 1, 'C');

    // Flight Information (Departure)
    $pdfDeparture->SetFont('helvetica', '', 12);
    $pdfDeparture->Ln(10);
    $pdfDeparture->Cell(0, 10, "Flight Number: $flightNumber", 0, 1);
    $pdfDeparture->Cell(0, 10, "Origin: $origin", 0, 1);
    $pdfDeparture->Cell(0, 10, "Destination: $destination", 0, 1);
    $pdfDeparture->Cell(0, 10, "Departure Time: $departureTime", 0, 1);
    $pdfDeparture->Cell(0, 10, "Arrival Time: $arrivalTime", 0, 1);
    $pdfDeparture->Cell(0, 10, "Amount: $flightPrice per passenger", 0, 1);

    // Add-ons (if any)
    if (!empty($selectedAddonsForConfirmation)) {
        $pdfDeparture->Ln(10);
        foreach ($selectedAddonsForConfirmation as $addon) {
            $addonName = $addon['Name'] ?? 'N/A';
            $addonPrice = $addon['Price'] ?? 0;
            $pdfDeparture->Cell(0, 10, "$addonName - \$" . number_format($addonPrice, 2), 0, 1);
        }
    }

    // Passenger Information
    $pdfDeparture->Ln(10);
    foreach ($passengerNames as $index => $name) {
        $pdfDeparture->Cell(0, 10, ($index + 1) . ". $name", 0, 1);
    }

    // Total Amount
    $pdfDeparture->Ln(10);
    $pdfDeparture->Cell(0, 10, "Total Amount: \$" . number_format($totalAmount, 2), 0, 1);

    // Save and download Departure Ticket PDF
    $ticketDir = $_SERVER['DOCUMENT_ROOT'] . '/ANGEL/etickets/';
    if (!is_dir($ticketDir)) {
        mkdir($ticketDir, 0777, true);
    }
    
    $ticketFileDeparture = $ticketDir . "eticket_departure_$reservationID.pdf";
    $pdfDeparture->Output($ticketFileDeparture, 'F');

    // Serve the Departure PDF file to the user
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($ticketFileDeparture) . '"');
    header('Content-Length: ' . filesize($ticketFileDeparture));
    readfile($ticketFileDeparture);

    unlink($ticketFileDeparture);  // Remove the temporary file after download
    exit;
}

// If the form is submitted for the return flight ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_return_ticket']) && $selectedReturnFlight) {
    // Generate Return Ticket
    $pdfReturn = new TCPDF();
    $pdfReturn->AddPage();
    $pdfReturn->SetFont('helvetica', 'B', 16);
    $pdfReturn->Cell(0, 10, "$airlineName - E-Ticket (Return)", 0, 1, 'C');

    // Flight Information (Return)
    $pdfReturn->SetFont('helvetica', '', 12);
    $pdfReturn->Ln(10);
    $pdfReturn->Cell(0, 10, "Flight Number: $returnFlightNumber", 0, 1);
    $pdfReturn->Cell(0, 10, "Origin: $returnOrigin", 0, 1);
    $pdfReturn->Cell(0, 10, "Destination: $returnDestination", 0, 1);
    $pdfReturn->Cell(0, 10, "Departure Time: $returnDepartureTime", 0, 1);
    $pdfReturn->Cell(0, 10, "Arrival Time: $returnArrivalTime", 0, 1);
    $pdfReturn->Cell(0, 10, "Amount: $returnFlightPrice per passenger", 0, 1);

    // Add-ons (if any)
    if (!empty($selectedAddonsForConfirmation)) {
        $pdfReturn->Ln(10);
        foreach ($selectedAddonsForConfirmation as $addon) {
            $addonName = $addon['Name'] ?? 'N/A';
            $addonPrice = $addon['Price'] ?? 0;
            $pdfReturn->Cell(0, 10, "$addonName - \$" . number_format($addonPrice, 2), 0, 1);
        }
    }

    // Passenger Information
    $pdfReturn->Ln(10);
    foreach ($passengerNames as $index => $name) {
        $pdfReturn->Cell(0, 10, ($index + 1) . ". $name", 0, 1);
    }

    // Total Amount
    $pdfReturn->Ln(10);
    $pdfReturn->Cell(0, 10, "Total Amount: \$" . number_format($totalAmountReturn, 2), 0, 1);

    // Save and download Return Ticket PDF
    $ticketDir = $_SERVER['DOCUMENT_ROOT'] . '/ANGEL/etickets/';
    if (!is_dir($ticketDir)) {
        mkdir($ticketDir, 0777, true);
    }
    
    $ticketFileReturn = $ticketDir . "eticket_return_$reservationID.pdf";
    $pdfReturn->Output($ticketFileReturn, 'F');

    // Serve the Return PDF file to the user
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($ticketFileReturn) . '"');
    header('Content-Length: ' . filesize($ticketFileReturn));
    readfile($ticketFileReturn);

    unlink($ticketFileReturn);  // Remove the temporary file after download
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_return_ticket'])) {
    // If no return flight is selected, inform the user
    echo "No return flight available for a one-way trip.";
    exit;
}

?>

<!-- HTML Code for Displaying Tickets -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight E-Tickets</title>
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
            <li><a href="logout.php">Logout</a></li>
            <li><a href="acc_account.php">Account</a></li>
            <li><a href="acc_dashboard.php">Home</a></li>
        </ul>
    </nav>
</header>

<div class="eticket">
    <div class="eticket-header">
        <h1><?php echo $airlineName; ?></h1>
        <p><strong>Departure E-Ticket</strong></p>
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
            <button type="submit" name="generate_departure_ticket">Print E-Ticket</button>
        </form>
    </div>
</div>


<!-- Check if return flight is selected -->
<?php if ($selectedReturnFlight): ?>
    <div class="eticket">
        <div class="eticket-header">
            <h1><?php echo $airlineName; ?></h1>
            <p><strong>Return E-Ticket</strong></p>
        </div>

        <?php if ($selectedReturnFlight): ?>
    <h3>Return Flight Information</h3>
    <p>Flight Number: <?php echo htmlspecialchars($selectedReturnFlight['Flight_Number'] ?? 'N/A'); ?></p>
    <p>Return Date: <?php echo htmlspecialchars($selectedReturnFlight['Departure_Date'] ?? 'N/A'); ?></p>
    <p>Origin: <?php echo htmlspecialchars($selectedReturnFlight['Origin'] ?? 'N/A'); ?></p>
    <p>Destination: <?php echo htmlspecialchars($selectedReturnFlight['Destination'] ?? 'N/A'); ?></p>
    <p>Amount: $<?php echo number_format($selectedReturnFlight['Amount'] ?? 0, 2); ?> per passenger</p>
<?php endif; ?>


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
            <p><strong>Total Amount:</strong> $<?php echo number_format($totalAmountReturn, 2); ?></p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <form method="post" action="">
                <button type="submit" name="generate_return_ticket">Print E-Ticket</button>
            </form>
        </div>
    </div>
<?php endif; ?>
</div>
</body>
</html>
