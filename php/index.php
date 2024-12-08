<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();  // Start the session to access session data
include 'db.php';  // Include the database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);


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
        header("Location: choose_flight.php");
        exit();  // Ensure no further code is executed after redirection
    } else {
        // If no flights are found, display a message
        echo "<p>No flights found matching your criteria.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel - Airline Reservation</title>
    <link rel="stylesheet" href="/AIR-ANGEL-PARTIAL/styles/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&family=Source+Serif+Pro:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script>
        // Function to toggle return date visibility
        function toggleReturnDate() {
            const roundTrip = document.getElementById('round_trip');
            const returnDateField = document.getElementById('return_date_container');
            
            // Check the state of the 'round_trip' checkbox and toggle visibility accordingly
            if (roundTrip.checked) {
                returnDateField.style.display = 'block'; // Show return date field for round trip
            } else {
                returnDateField.style.display = 'none'; // Hide return date field for one way
            }
        }

        // Run the function once on page load to set the initial state
        document.addEventListener('DOMContentLoaded', toggleReturnDate);

        // Attach event listener to the checkbox to toggle on change
        document.getElementById('round_trip').addEventListener('change', toggleReturnDate);
    
        window.addEventListener('scroll', function () {
            const section1 = document.querySelector('.section-1');
            const scrollPosition = window.scrollY;

            // Create a parallax effect by adjusting the background position
            section1.style.backgroundPosition = `center ${scrollPosition * -0.5}px`;
        });
    </script>

</head>
<body>
    <main>
        <header>
            <div class="header">
                <!-- Logo Section -->
                <div class="logo-container">
                    <img id="logo-img" src="/AIR-ANGEL-PARTIAL/assets/images/logo.png" alt="AirAngel Logo">
                    <h1 id="logo-text">AirAngel</h1>
                </div>
        
                <!-- Main Navigation -->
                <nav class="navbar">
                    <ul>
                        <li><a href="index.php">Book</a></li>
                        <li><a href="index.php">Explore</a></li>
                        <li><a href="index.php">Manage</a></li>
                        <li><a href="index.php">About</a></li>
                    </ul>
                </nav>
        
                <!-- Authentication Links (Sign In / Sign Up / Logout) -->
                <nav class="auth-nav">
                    <ul>
                        <?php if (!$is_logged_in): ?>
                            <li><a href="signin.php">Sign In</a></li>
                            <li><a href="signup.php">Sign Up</a></li>
                        <?php else: ?>
                            <li><a href="logout.php">Logout</a></li>
                            <li><a href="account.php">Account</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
        
        <div class="section section-1">
            <div class="center-text-container">
                <h1>Fly high through safe skies</h1>
                <h3>Book now!</h3>
            </div>
            
            <section class="booking-form-section">
                <h2>Book Your Flight</h2>
                <form method="POST">
                    <!-- Flight Type Selection -->
                    <fieldset>
                        <legend>Select Flight Type</legend>
                        <div class="radio-group">
                            <div class="option">
                                <input type="radio" id="one_way" name="flight_type" value="One Way" onclick="toggleReturnDate()" required>
                                <label for="one_way">One Way</label>
                            </div>
                            <div class="option">
                                <input type="radio" id="round_trip" name="flight_type" value="Round Trip" onclick="toggleReturnDate()">
                                <label for="round_trip">Round Trip</label>
                            </div>                            
                        </div>
                    </fieldset>

                    <!-- Departure Location and Destination -->
                    <div class="location"> 
                        <label for="from" style="margin-right: 2px; margin-left: 5px">From:</label>
                        <input type="text" id="from" name="from" placeholder="Departure City" required>

                        <label for="to" style="margin-right: 2px; margin-left: 5px">To:</label>
                        <input type="text" id="to" name="to" placeholder="Destination City" required>
                    </div>

                    <!-- Departure Time -->
                    <div class="date">
                        <div id="depart_date_container" class="return-date-container">
                            <label for="depart_date" style="margin-right: 2px; margin-left: 5px">Departure Date:</label>
                            <input type="date" id="depart_date" name="depart_date" required>
                        </div>

                        <!-- Return Date (Visible only for Round Trip) -->
                        <div id="return_date_container" class="return-date-container">
                            <label for="return_date" style="margin-right: 2px; margin-left: 5px">Return Date:</label>
                            <input type="date" id="return_date" name="return_date">
                        </div>
                    </div>



                    <!-- Search Button -->
                    <button id="search-button" type="submit">Search Flight</button>
                </form>
            </section>
        </div>

        <div class="section section-2">
            <h1>Explore</h1>
            <div class="image-container">
                <a href=""><img src="/AIR-ANGEL-PARTIAL/assets/images/Card.png"></a>
                <a href=""><img src="/AIR-ANGEL-PARTIAL/assets/images/Cardo.png"></a>
                <a href=""><img src="/AIR-ANGEL-PARTIAL/assets/images/Carding.png"></a>
            </div>
            <button>Discover More</button>
        </div>

        <div class="section section-3">
            <h1>Our Services</h1>
            <div class="container">
                <div class="choice">
                    <a href="">
                        <img src="/AIR-ANGEL-PARTIAL/assets/images/Luggage.png">
                        <h3>Luggage</h3>
                    </a>
                    
                </div>
                
                <div class="choice">
                    <a href="">
                        <img src="/AIR-ANGEL-PARTIAL/assets/images/Aircraft Seat Middle.png">
                        <h3>Seat Selection</h3>
                    </a>                    
                </div>

                <div class="choice">
                    <a href="">
                        <img src="/AIR-ANGEL-PARTIAL/assets/images/Buffet Breakfast.png">
                        <h3>Food</h3>
                    </a>                    
                </div>
            </div>
        </div>

        <div class="section section-4">
            <h1>About Us</h1>
            <p id="about-us">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolorum quaerat eius at! Itaque impedit quis atque consequuntur nihil ducimus, non ad unde, perferendis magni eligendi illum sunt inventore nobis quaerat?</p>
            <button>Book a Flight Now!</button>
        </div>
    </main>
</body>
</html>
