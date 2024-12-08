<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();  // Start the session to access session data

// Check if the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);

// Redirect to search if required session data is missing
if (!isset($_SESSION['available_flights']) || empty($_SESSION['available_flights']) ||
    !isset($_SESSION['origin']) || !isset($_SESSION['destination'])) {
    header("Location: search_flights.php"); // Redirect to flight search page
    exit();
}

// Extract session variables
$origin = $_SESSION['origin'];
$destination = $_SESSION['destination'];
$flight_type = $_SESSION['flight_type'] ?? 'One Way';  // Default to One Way
$available_flights = $_SESSION['available_flights'];

// Store the selected flight in the session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_flight'])) {
    $selectedFlight = $_POST['selected_flight']; // Capture flight ID from form
    // You need to fetch the full flight data (assuming available_flights is an array of flight details)
    foreach ($available_flights as $flight) {
        if ($flight['Available_Flights_Number_ID'] == $selectedFlight) {
            $_SESSION['selected_flight'] = $flight;  // Store full flight details in session
            break;
        }
    }
    header("Location: passengerinfo.php");  // Redirect to passenger info page
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Flight</title>
    <script>
        // Client-side validation for flight selection
        function validateSelection() {
            const selectedFlights = document.querySelectorAll('input[type="radio"]:checked');
            if (selectedFlights.length === 0) {
                alert('Please select at least one flight.');
                return false;
            }
            return true;
        }

        // Go back to the previous page
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div style="margin: 0 auto; width: 80%; padding: 20px; font-family: Arial, sans-serif;">
        <h1>Select Your Flight</h1>
        <div style="text-align: right; margin-bottom: 20px;">
            <?php if (!$is_logged_in): ?>
                <a href="signin.php">Sign In</a> | <a href="signup.php">Sign Up</a>
            <?php else: ?>
                <a href="logout.php">Logout</a> | <a href="account.php">Account</a>
            <?php endif; ?>
        </div>

        <form method="POST" action="choose_flight.php" onsubmit="return validateSelection();">
            <?php if ($flight_type == 'Round Trip'): ?>
                <h2>Departure Flight</h2>
                <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <tr>
                        <th>Flight Number</th>
                        <th>Departure Date</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Amount</th>
                        <th>Select</th>
                    </tr>
                    <?php foreach ($available_flights as $flight):
                        if ($flight['Origin'] == $origin && $flight['Destination'] == $destination): ?>
                            <tr>
                                <td><?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                                <td><?= htmlspecialchars($flight['Origin']) ?></td>
                                <td><?= htmlspecialchars($flight['Destination']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Amount']) ?></td>
                                <td><input type="radio" name="selected_departure_flight" value="<?= $flight['Available_Flights_Number_ID'] ?>"></td>
                            </tr>
                        <?php endif; endforeach; ?>
                </table>

                <h2>Return Flight</h2>
                <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <tr>
                        <th>Flight Number</th>
                        <th>Departure Date</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Amount</th>
                        <th>Select</th>
                    </tr>
                    <?php foreach ($available_flights as $flight):
                        if ($flight['Origin'] == $destination && $flight['Destination'] == $origin): ?>
                            <tr>
                                <td><?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                                <td><?= htmlspecialchars($flight['Origin']) ?></td>
                                <td><?= htmlspecialchars($flight['Destination']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Amount']) ?></td>
                                <td><input type="radio" name="selected_return_flight" value="<?= $flight['Available_Flights_Number_ID'] ?>"></td>
                            </tr>
                        <?php endif; endforeach; ?>
                </table>
            <?php else: ?>
                <h2>Available Flights</h2>
                <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <tr>
                        <th>Flight Number</th>
                        <th>Departure Date</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Amount</th>
                        <th>Select</th>
                    </tr>
                    <?php foreach ($available_flights as $flight):
                        if ($flight['Origin'] == $origin && $flight['Destination'] == $destination): ?>
                            <tr>
                                <td><?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                                <td><?= htmlspecialchars($flight['Origin']) ?></td>
                                <td><?= htmlspecialchars($flight['Destination']) ?></td>
                                <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                                <td><?= htmlspecialchars($flight['Amount']) ?></td>
                                <td><input type="radio" name="selected_flight" value="<?= $flight['Available_Flights_Number_ID'] ?>"></td>
                            </tr>
                        <?php endif; endforeach; ?>
                </table>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <button type="button" onclick="goBack()">Go Back</button>
                <button type="submit">Book Selected Flight</button>
            </div>
        </form>
    </div>
</body>
</html>
