<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);

// If the user is logged in and has already selected a flight, redirect them to choose_flight.php
if ($is_logged_in && isset($_SESSION['available_flights']) && !empty($_SESSION['available_flights'])) {
    header("Location: choose_flight.php");
    exit();  // Ensure no further code is executed after redirection
}

// Process flight search form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form and sanitize them
    $departure_date = $_POST['depart_time'] ?? '';  // Handle undefined inputs
    $origin = $_POST['from'] ?? '';  // Handle undefined inputs
    $destination = $_POST['to'] ?? '';  // Handle undefined inputs
    $flight_type = $_POST['flight_type'] ?? '';  // Handle undefined inputs
    
    // Since there's no return date column, we use the departure date as the return date
    $return_date = $_POST['return_date'] ?? $departure_date;  // Handle undefined return date

    // If the form is incomplete, redirect to the choose_flight.php page
    if (empty($departure_date) || empty($origin) || empty($destination) || empty($flight_type)) {
        header("Location: choose_flight.php");
        exit();  // Ensure no further code is executed after redirection
    }

    // Query the Available_Flights table based on user input
    $sql = "SELECT * FROM Available_Flights WHERE Departure_Date = ? AND Origin = ? AND Destination = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $departure_date, $origin, $destination);
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If flights are found, store them in the session for use in the choose_flight.php page
        $_SESSION['available_flights'] = [];
        while ($row = $result->fetch_assoc()) {
            $_SESSION['available_flights'][] = $row;  // Store each available flight in the session
        }
        
        // Set session variables for origin, destination, and flight type
        $_SESSION['origin'] = $origin;
        $_SESSION['destination'] = $destination;
        $_SESSION['departure_date'] = $departure_date;
        $_SESSION['return_date'] = $return_date;
        $_SESSION['flight_type'] = $flight_type;

        // Redirect to choose_flight.php after successful submission
        header("Location: choose_flight.php");
        exit();  // Ensure no further code is executed after redirection
    } else {
        // Redirect to choose_flight.php even if no flights are found
        header("Location: choose_flight.php");
        exit();  // Ensure no further code is executed after redirection
    }
}

// Fetch all available flights from the database (no search criteria)
$sql = "SELECT * FROM Available_Flights";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();
$all_flights = [];

if ($result->num_rows > 0) {
    // Store all available flights in an array
    while ($row = $result->fetch_assoc()) {
        $all_flights[] = $row;
    }
} else {
    $all_flights = [];  // If no flights found, initialize as empty array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel - Airline Reservation</title>
    <script>
        // Toggle the visibility of the return date field based on flight type selection
        function toggleReturnDate() {
            const roundTrip = document.getElementById('round_trip');
            const returnDateField = document.getElementById('return_date_container');
            if (roundTrip.checked) {
                returnDateField.style.display = 'block'; // Show return date field for round trip
            } else {
                returnDateField.style.display = 'none'; // Hide return date field for one way
            }
        }
    </script>
    <link rel="stylesheet" href="/ANGEL/styles/user_dashboard.css">

</head>
<body>
    <header>
    <h1>Welcome to AirAngel!</h1> 

    <ul>
    <?php if (!$is_logged_in): ?>
        <li><a href="signin.php">Sign In</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li> <!-- Show Logout if logged in -->
        <li><a href="account.php">Account</a></li>
    <?php endif; ?>
    </ul>
    </header>
    <main>
    <section>
    <h2>Book Your Flight</h2>
    <form method="POST">
        <!-- Flight Type Selection -->
        <label for="flight_type">Select Flight Type:</label><br>
        <input type="radio" id="one_way" name="flight_type" value="One Way" onclick="toggleReturnDate()" >
        <label for="one_way">One Way</label><br>
        <input type="radio" id="round_trip" name="flight_type" value="Round Trip" onclick="toggleReturnDate()">
        <label for="round_trip">Round Trip</label><br>

        <!-- Departure Location and Destination -->
        <label for="from">From:</label>
        <input type="text" id="from" name="from" placeholder="Departure City" ><br><br>

        <label for="to">To:</label>
        <input type="text" id="to" name="to" placeholder="Destination City"><br><br>

        <!-- Departure Time -->
        <label for="depart_time">Departure Date:</label>
        <input type="date" id="depart_time" name="depart_time"><br><br>

        <!-- Return Date (Visible only for Round Trip) -->
        <div id="return_date_container" style="display: none;">
            <label for="return_date">Return Date:</label>
            <input type="date" id="return_date" name="return_date"><br><br>
        </div>

        <!-- Search Button -->
        <button type="submit">Search Flight</button>
    </form>
    </section>
    <!-- Display All Available Flights -->
    <section class="available-flights-section">
            <h2>All Available Flights</h2>
            <?php if (count($all_flights) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Flight Number</th>
                            <th>Departure Date</th>
                            <th>Arrival Date</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Departure Time</th>
                            <th>Arrival Time</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_flights as $flight): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($flight['Flight_Number']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Departure_Date']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Arrival_Date']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Origin']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Destination']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Departure_Time']); ?></td>
                                <td><?php echo htmlspecialchars($flight['Arrival_Time']); ?></td>
                                <td><?php echo "$" . htmlspecialchars($flight['Amount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No flights available at the moment.</p>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
