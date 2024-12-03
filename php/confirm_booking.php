<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();

$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
$available_flights = $_SESSION['available_flights'];
$flight_details = $_SESSION['flight_details'] ?? null;

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['selected_flight']) && (!isset($_POST['selected_departure_flight']) || !isset($_POST['selected_return_flight']))) {
        echo "<p>No flight selected. Please go back and select a flight.</p>";
        exit();
    }

    if (isset($_POST['selected_flight'])) {
        foreach ($available_flights as $flight) {
            if ($flight['Available_Flights_Number_ID'] == $_POST['selected_flight']) {
                $_SESSION['flight_details'] = $flight;
                $flight_details = $flight;
                break;
            }
        }
    } else {
        $_SESSION['flight_details'] = [
            'departure' => null,
            'return' => null
        ];
        foreach ($available_flights as $flight) {
            if ($flight['Available_Flights_Number_ID'] == $_POST['selected_departure_flight']) {
                $_SESSION['flight_details']['departure'] = $flight;
            }
            if ($flight['Available_Flights_Number_ID'] == $_POST['selected_return_flight']) {
                $_SESSION['flight_details']['return'] = $flight;
            }
        }
        $flight_details = $_SESSION['flight_details'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>
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
    <h1>Flight Booking System</h1>
    <ul>
    <?php if (!$is_logged_in): ?>
        <li><a href="signin.php">Sign In</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li> <!-- Show Logout if logged in -->
        <li><a href="account.php">Account</a></li>
    <?php endif; ?>
    </ul>

    <?php if (!$flight_details): ?>
        <h2>Select Your Flight</h2>
        <form method="POST">
            <h3>Departure Flights</h3>
            <table border="1">
                <tr>
                    <th>Flight Number</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Amount</th>
                    <th>Select</th>
                </tr>
                <?php foreach ($available_flights as $flight): ?>
                    <?php if ($flight['Origin'] === $_SESSION['origin'] && $flight['Destination'] === $_SESSION['destination']): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['Available_Flights_Number_ID']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Origin']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Date']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Amount']); ?></td>
                            <td><input type="radio" name="selected_flight" value="<?php echo $flight['Available_Flights_Number_ID']; ?>"></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
            <button type="submit">Confirm Flight</button>
        </form>
    <?php else: ?>
        <h2>Flight Details</h2>
        <ul>
            <li>Flight Number: <?php echo htmlspecialchars($flight_details['Available_Flights_Number_ID'] ?? ''); ?></li>
            <li>Origin: <?php echo htmlspecialchars($flight_details['Origin']); ?></li>
            <li>Destination: <?php echo htmlspecialchars($flight_details['Destination']); ?></li>
            <li>Date: <?php echo htmlspecialchars($flight_details['Departure_Date']); ?></li>
        </ul>

        <h2>Choose Payment Method</h2>
        <form method="POST">
            <label><input type="radio" name="payment_method" value="Cash" onclick="toggleCashPay()"> Cash</label><br>
            <label><input type="radio" name="payment_method" value="ECash" onclick="toggleEcashPay()"> ECash</label><br>
            <label><input type="radio" name="payment_method" value="Card" onclick="toggleCardPay()"> Card</label><br>

            <div id="cash_payment" style="display: none;">
                <h3>Pay at a branch</h3>
                <p>Take the flight details and pay at a branch. Your reference ID is: 12345.</p>
            </div>
            <div id="ecash_payment" style="display: none;">
                <h3>Here is the account number:0963874825383</h3>

            </div>
            <div id="card_payment" style="display: none;">
                <h3>Enter Card Details</h3>
                <label>Card Number: <input type="text" name="card_number"></label><br>
                <label>Expiry Date: <input type="month" name="expiry_date"></label><br>
                <label>CVV: <input type="text" name="cvv"></label>
            </div>

            <button type="submit">Submit Payment</button>
        </form>
    <?php endif; ?>
</body>
</html>
