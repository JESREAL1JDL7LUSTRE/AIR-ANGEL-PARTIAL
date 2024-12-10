<?php
session_start();
include('db.php'); // Include your database connection

// Check if flight ID exists in the session
if (!isset($_SESSION['selected_flight_id'])) {
    echo "Error: No flight selected. Please go back and choose a flight.";
    exit;
}

$selectedAddonsForConfirmation = $_SESSION['selected_addons_for_confirmation'] ?? [];

$selectedFlightID = $_SESSION['selected_flight_id'];
$selected_return_flight_id = $_SESSION['selected_return_flight_id'] ?? null;  // Handle return flight, if exists

// Fetch departure flight details
$stmt = $conn->prepare("SELECT * FROM Available_Flights WHERE Available_Flights_Number_ID = ?");
$stmt->bind_param("i", $selectedFlightID);
$stmt->execute();
$result = $stmt->get_result();
$selectedFlight = $result->fetch_assoc();

if ($selectedFlight) {
    // Store departure flight details in session
    $_SESSION['selected_flight'] = $selectedFlight; 
} else {
    echo "Error: Selected flight not found in the database.";
    exit;
}

// Fetch return flight details if available
$selectedReturnFlight = null;
if ($selected_return_flight_id) {
    $stmt = $conn->prepare("SELECT * FROM Available_Flights WHERE Available_Flights_Number_ID = ?");
    $stmt->bind_param("i", $selected_return_flight_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedReturnFlight = $result->fetch_assoc();
    
    if ($selectedReturnFlight) {
        // Store return flight details in session
        $_SESSION['selected_return_flight'] = $selectedReturnFlight;
    } else {
        echo "Error: Selected return flight not found in the database.";
        exit;
    }
}

// Fetch session data
$selectedFlight = $_SESSION['selected_flight'] ?? null;
$selectedReturnFlight = $_SESSION['selected_return_flight'] ?? null;
$numPassengers = $_SESSION['num_passengers'] ?? 0;
$selectedAddons = $_SESSION['selected_addons'] ?? [];
$passenger_ids = $_SESSION['passenger_ids'] ?? []; // Array of Passenger IDs

// Ensure the user is logged in and get account ID
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");
    exit;
}

$account_id = $_SESSION['Account_ID'] ?? null;
if (!$account_id) {
    die("Error: Account not found.");
}

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



// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? null;

    if (!$paymentMethod) {
        die("Error: Please select a payment method.");
    }

    $conn->begin_transaction(); // Begin a transaction

    try {
        // Insert payment into Payment table
        $stmt = $conn->prepare("
            INSERT INTO Payment (Payment_Amount, Payment_Date, Payment_Method_Name) 
            VALUES (?, NOW(), ?)
        ");
        $stmt->bind_param("ds", $totalPrice, $paymentMethod);
        if (!$stmt->execute()) {
            error_log("Error inserting Payment: " . $stmt->error);
            throw new Exception("Failed to insert payment.");
        }
        $payment_id = $conn->insert_id;
        error_log("Generated Payment ID: $payment_id");
        $_SESSION['payment_id'] = $payment_id; // Store Payment ID in session

        // Insert reservation into Reservation table
        $stmt = $conn->prepare("
            INSERT INTO Reservation (Booking_date, Payment_ID_FK) 
            VALUES (NOW(), ?)
        ");
        $stmt->bind_param("i", $payment_id);
        if (!$stmt->execute()) {
            error_log("Error inserting Reservation: " . $stmt->error);
            throw new Exception("Failed to insert reservation.");
        }
        $reservation_id = $stmt->insert_id;
        error_log("Generated Reservation ID: $reservation_id");
        $_SESSION['reservation_id'] = $reservation_id; // Store Reservation ID in session

        // Insert passengers into Reservation_to_Passenger table
        $reservation_to_passenger_ids = [];
        $stmt = $conn->prepare("
            INSERT INTO Reservation_to_Passenger (Passenger_ID_FK, Reservation_ID_FK) 
            VALUES (?, ?)
        ");
        foreach ($passenger_ids as $passenger_id) {
            $stmt->bind_param("ii", $passenger_id, $reservation_id);
            if (!$stmt->execute()) {
                error_log("Error inserting Reservation_to_Passenger: " . $stmt->error);
                throw new Exception("Failed to insert reservation-to-passenger record.");
            }
            $reservation_to_passenger_ids[] = $stmt->insert_id;
        }
        error_log("Generated Reservation_to_Passenger IDs: " . implode(", ", $reservation_to_passenger_ids));

        // Insert into flight_to_reservation_to_passenger table for departure flight
        $stmt = $conn->prepare("
            INSERT INTO flight_to_reservation_to_passenger (Flight_to_Reservation_ID_FK, Available_Flights_Number_ID_FK) 
            VALUES (?, ?)
        ");
        $frp_number_ids = []; // Store generated FRP_Number_IDs
        foreach ($reservation_to_passenger_ids as $reservation_to_passenger_id) {
            $stmt->bind_param("ii", $reservation_to_passenger_id, $selectedFlight['Available_Flights_Number_ID']);
            if (!$stmt->execute()) {
                error_log("Error inserting Flight_to_Reservation_to_Passenger (departure): " . $stmt->error);
                throw new Exception("Failed to insert flight-to-reservation record for departure.");
            }
            $frp_number_ids[] = $stmt->insert_id; // Store generated FRP_Number_ID
        }

        // Insert into flight_to_reservation_to_passenger table for return flight (if applicable)
        if ($selectedReturnFlight) {
            foreach ($reservation_to_passenger_ids as $reservation_to_passenger_id) {
                $stmt->bind_param("ii", $reservation_to_passenger_id, $selectedReturnFlight['Available_Flights_Number_ID']);
                if (!$stmt->execute()) {
                    error_log("Error inserting Flight_to_Reservation_to_Passenger (return): " . $stmt->error);
                    throw new Exception("Failed to insert flight-to-reservation record for return.");
                }
                $frp_number_ids[] = $stmt->insert_id; // Store generated FRP_Number_ID
            }
        }

// Fetch the add-ons selected for confirmation
$selectedAddonsForConfirmation = $_SESSION['selected_addons_for_confirmation'] ?? [];

// Insert add-ons into Add_on table
$stmt = $conn->prepare("
    INSERT INTO add_on (FRP_Number_ID_FK, Seat_Selector_ID_FK, Food_ID_FK, Baggage_ID_FK) 
    VALUES (?, ?, ?, ?)
");

foreach ($selectedAddonsForConfirmation as $addon) {
    // Ensure the addon data is valid (check if the IDs are not null)
    $seat_selector_id = null;
    $food_id = null;
    $baggage_id = null;

    // Assign Seat_Selector_ID_FK, Food_ID_FK, Baggage_ID_FK based on addon type
    if ($addon['Type'] === 'SeatSelector') {
        $seat_selector_id = $addon['ID'];  // Assign SeatSelector ID
    } elseif ($addon['Type'] === 'Food') {
        $food_id = $addon['ID'];  // Assign Food ID
    } elseif ($addon['Type'] === 'Baggage') {
        $baggage_id = $addon['ID'];  // Assign Baggage ID
    }

    // For each generated FRP_Number_ID, insert the add-on
    foreach ($frp_number_ids as $frp_number_id) {
        $stmt->bind_param("iiii", $frp_number_id, $seat_selector_id, $food_id, $baggage_id);
        if (!$stmt->execute()) {
            error_log("Error inserting Add_on: " . $stmt->error);
            throw new Exception("Failed to insert add-on record.");
        }
    }
}

        // Insert into Reservation_to_Account table
        $stmt = $conn->prepare("
            INSERT INTO Reservation_to_Account (Reservation_ID_FK, Account_ID_FK) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $reservation_id, $account_id);
        if (!$stmt->execute()) {
            error_log("Error inserting Reservation_to_Account: " . $stmt->error);
            throw new Exception("Failed to insert reservation-to-account record.");
        }

        $conn->commit(); // Commit the transaction
        error_log("Transaction committed successfully.");
        header("Location: acc_booking.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction on error
        die("Error: Could not complete the payment and reservation process. " . $e->getMessage());
    }
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

        function goBack() {
            window.history.back();
        }
    </script>
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
        </div>
    </header>
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
        <button type="button" onclick="goBack()">Go Back</button>
    </form>
</body>
</html>
