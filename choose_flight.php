<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();  // Start the session to access session data

// Check if the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);

// Check if the flights data exists in the session

if (!isset($_SESSION['available_flights']) || empty($_SESSION['available_flights'])) {
    echo "<p>No available flights to choose from.</p>";
    exit();
}

// Check if origin and destination session variables are set
if (isset($_SESSION['origin']) && isset($_SESSION['destination'])) {
    $origin = $_SESSION['origin'];
    $destination = $_SESSION['destination'];
    $flight_type = $_SESSION['flight_type'] ?? 'One Way';  // Default to One Way if not set
} else {
    echo "<p>Origin and destination are not set. Please perform a flight search first.</p>";
    exit();
}

// Get available flights from session
$available_flights = $_SESSION['available_flights'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Flight</title>
    <script>
        is 
        function goBack() {
            window.location.href = 'user_dashboard.php';
        }
    </script>
</head>
<body>
    <h1>Select Your Flight</h1>

    <ul>
    <?php if (!$is_logged_in): ?>
        <li><a href="signin.php">Sign In</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li> <!-- Show Logout if logged in -->
        <li><a href="account.php">Account</a></li>
    <?php endif; ?>
    </ul>


    <form method="POST" action="confirm_booking.php">
        
        <?php if ($flight_type == 'Round Trip'): ?>
            <h2>Departure Flight</h2>
            <table border="1">
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
                // Display only departure flights (origin to destination)
                foreach ($available_flights as $flight):
                    if ($flight['Origin'] == $origin && $flight['Destination'] == $destination):
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['Available_Flights_Number_ID']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Date']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Origin']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Arrival_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Amount']); ?></td>
                            <td><input type="radio" name="selected_departure_flight" value="<?php echo $flight['Available_Flights_Number_ID']; ?>"></td>
                        </tr>
                <?php endif; endforeach; ?>
            </table>

            <h2>Return Flight</h2>
            <table border="1">
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
                // Display only return flights (destination to origin)
                foreach ($available_flights as $flight):
                    if ($flight['Origin'] == $destination && $flight['Destination'] == $origin):
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['Available_Flights_Number_ID']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Date']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Origin']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Arrival_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Amount']); ?></td>
                            <td><input type="radio" name="selected_return_flight" value="<?php echo $flight['Available_Flights_Number_ID']; ?>"></td>
                        </tr>
                <?php endif; endforeach; ?>
            </table>
        <?php else: ?>
            <h2>Available Flights</h2>
            <table border="1">
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
                // Display one-way flights (only origin to destination)
                foreach ($available_flights as $flight):
                    if ($flight['Origin'] == $origin && $flight['Destination'] == $destination):
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['Available_Flights_Number_ID']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Date']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Origin']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Destination']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Departure_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Arrival_Time']); ?></td>
                            <td><?php echo htmlspecialchars($flight['Amount']); ?></td>
                            <td><input type="radio" name="selected_flight" value="<?php echo $flight['Available_Flights_Number_ID']; ?>"></td>
                        </tr>
                <?php endif; endforeach; ?>
            </table>
        <?php endif; ?>

        <button type="submit">Book Selected Flight</button>
    </form>
    <h2> </h2>
    <button type="button" onclick="goBack()">Go Back</button>
</body>
</html>
