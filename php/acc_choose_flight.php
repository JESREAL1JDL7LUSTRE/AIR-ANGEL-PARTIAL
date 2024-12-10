<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();  // Start the session to access session data
include('db.php'); // Include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['Account_Email']) || empty($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Redirect to search if required session data is missing
if (!isset($_SESSION['available_flights']) || empty($_SESSION['available_flights']) ||
    !isset($_SESSION['origin']) || !isset($_SESSION['destination'])) {
    header("Location: acc_dashboard.php"); // Redirect to flight search page
    exit();
}

// Extract session variables
$origin = $_SESSION['origin'];
$destination = $_SESSION['destination'];
$flight_type = $_SESSION['flight_type'] ?? 'One Way';  // Default to One Way
$available_flights = $_SESSION['available_flights'];
$returndestination = $_SESSION['origin'];
$returnorigin = $_SESSION['destination'];

// Handle flight selection form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_flight'])) {
    $selectedFlight = $_POST['selected_flight']; // Capture flight ID from form
    // You need to fetch the full flight data (assuming available_flights is an array of flight details)
    foreach ($available_flights as $flight) {
        if ($flight['Available_Flights_Number_ID'] == $selectedFlight) {
            $_SESSION['selected_flight'] = $flight;  // Store full flight details in session
            break;
        }
    }
    header("Location: acc_passenger_info.php");  // Redirect to passenger info page
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
            const flightType = "<?= htmlspecialchars($flight_type) ?>"; // Dynamically include PHP variable
            const departureFlight = document.querySelector('input[name="selected_departure_flight"]:checked');
            const returnFlight = document.querySelector('input[name="selected_return_flight"]:checked');

            if (!departureFlight) {
                alert('Please select a departure flight.');
                return false;
            }

            if (flightType === 'Round Trip' && !returnFlight) {
                alert('Please select a return flight.');
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

<div style="margin: 0 auto; width: 80%; padding: 20px; font-family: Arial, sans-serif;">
    <h1>Select Your Flight</h1>

    <form method="POST" action="acc_choose_flight.php" onsubmit="return validateSelection();">
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
                <?php 
                foreach ($available_flights as $flight):
                    if ($flight['Origin'] === $origin && $flight['Destination'] === $destination): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['Flight_Number']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                            <td><?= htmlspecialchars($flight['Origin']) ?></td>
                            <td><?= htmlspecialchars($flight['Destination']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Amount']) ?></td>
                            <td>
                                <input type="radio" name="selected_departure_flight" 
                                    value="<?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?>">
                            </td>
                        </tr>
                    <?php endif;
                endforeach; ?>
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
                <?php 
                if (!empty($_SESSION['return_flights'])) {
                    foreach ($_SESSION['return_flights'] as $flight): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['Flight_Number']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                            <td><?= htmlspecialchars($flight['Origin']) ?></td>
                            <td><?= htmlspecialchars($flight['Destination']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Amount']) ?></td>
                            <td>
                                <input type="radio" name="selected_return_flight" 
                                    value="<?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?>">
                            </td>
                        </tr>
                    <?php endforeach;
                } else {
                    echo "<tr><td colspan='8'>No return flights available for the selected criteria.</td></tr>";
                }
                ?>
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
                    if ($flight['Origin'] === $origin && $flight['Destination'] === $destination): ?>
                        <tr>
                            <td><?= htmlspecialchars($flight['Flight_Number']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Date']) ?></td>
                            <td><?= htmlspecialchars($flight['Origin']) ?></td>
                            <td><?= htmlspecialchars($flight['Destination']) ?></td>
                            <td><?= htmlspecialchars($flight['Departure_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Arrival_Time']) ?></td>
                            <td><?= htmlspecialchars($flight['Amount']) ?></td>
                            <td>
                                <input type="radio" name="selected_departure_flight" 
                                    value="<?= htmlspecialchars($flight['Available_Flights_Number_ID']) ?>">
                            </td>
                        </tr>
                    <?php endif;
                endforeach; ?>
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
