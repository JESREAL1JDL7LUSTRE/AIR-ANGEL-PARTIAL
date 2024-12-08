<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();  // Start the session to access session data
include('db.php'); // Include your database connection

// Ensure the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form and sanitize them
    $departure_date = $_POST['depart_date'];
    $origin = $_POST['from'];
    $destination = $_POST['to'];
    $flight_type = $_POST['flight_type'];
    
    // Since there's no return date column, we use the departure date as the return date
    $return_date = $_POST['return_date'] ?? $departure_date;  // Handle undefined return date

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
        header("Location: acc_choose_flight.php");
        exit();  // Ensure no further code is executed after redirection
    } else {
        // If no flights are found, display a message
        echo "<p>No flights found matching your criteria.</p>";
    }
}
// Check if a search term is provided
$search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Modify the SQL query to filter based on the search term
$sql = "SELECT * FROM Available_Flights WHERE 
        Flight_Number LIKE ? OR 
        Origin LIKE ? OR 
        Destination LIKE ? OR 
        Departure_Date LIKE ?";

// Prepare and execute the query with the search term
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();
$all_flights = [];

if ($result->num_rows > 0) {
    // Store all matching flights in an array
    while ($row = $result->fetch_assoc()) {
        $all_flights[] = $row;
    }
} else {
    $all_flights = [];  // If no flights match, initialize as empty array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel - Airline Reservation</title>
    <link rel="stylesheet" href="/ANGEL/styles/index.css">
    <script>
        // Toggle the visibility of the return date field with animation
        function toggleReturnDate() {
            const roundTrip = document.getElementById('round_trip');
            const returnDateField = document.getElementById('return_date_container');
            if (roundTrip.checked) {
                returnDateField.classList.add('show'); // Add the class to animate and show
            } else {
                returnDateField.classList.remove('show'); // Remove the class to hide
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="/ANGEL/assets/images/logo.png" alt="AirAngel Logo">
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


    <main>
        <section class="booking-form-section">
            <h2>Book Your Flight</h2>
            <form method="POST">
                <!-- Flight Type Selection -->
                <fieldset>
                    <legend>Select Flight Type:</legend>
                    <div class="radio-group">
                        <input type="radio" id="one_way" name="flight_type" value="One Way" onclick="toggleReturnDate()" required>
                        <label for="one_way">One Way</label>
                        <input type="radio" id="round_trip" name="flight_type" value="Round Trip" onclick="toggleReturnDate()">
                        <label for="round_trip">Round Trip</label>
                    </div>
                </fieldset>

                <!-- Departure Location and Destination -->
                <label for="from">From:</label>
                <input type="text" id="from" name="from" placeholder="Departure City" required>

                <label for="to">To:</label>
                <input type="text" id="to" name="to" placeholder="Destination City" required>

                <!-- Departure Time -->
                <label for="depart_date">Departure Date:</label>
                <input type="date" id="depart_date" name="depart_date" required>

                <!-- Return Date (Visible only for Round Trip) -->
                <div id="return_date_container" class="return-date-container">
                    <label for="return_date">Return Date:</label>
                    <input type="date" id="return_date" name="return_date">
                </div>

                <!-- Search Button -->
                <button type="submit">Search Flight</button>
            </form>
        </section>

        <!-- Display All Available Flights -->
        <section class="available-flights-section">
    <h2>All Available Flights</h2>

    <!-- Search Bar -->
    <form method="GET" action="">
        <label for="search">Search Flights:</label>
        <input type="text" id="search" name="search" placeholder="Search by flight number, origin, destination..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

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
        <p>No flights match your search criteria.</p>
    <?php endif; ?>
</section>

    </main>
</body>
</html>