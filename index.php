<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel - Airline Reservation</title>
    <script>
        // JavaScript to show/hide return date based on flight type selection
        function toggleReturnDate() {
            const roundTrip = document.getElementById('round_trip');
            const returnDateField = document.getElementById('return_date_container');

            if (roundTrip.checked) {
                returnDateField.style.display = 'block'; // Show return date field
            } else {
                returnDateField.style.display = 'none'; // Hide return date field
            }
        }
    </script>
</head>
<body>
    <h1>Welcome to AirAngel!</h1> 
    <ul>
        <li><a href="signin.php">Sign In</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    </ul>

    <h2>Book Your Flight</h2>
    <form method="POST">
        <!-- Flight Type Selection -->
        <label for="flight_type">Select Flight Type:</label><br>
        <input type="radio" id="one_way" name="flight_type" value="One Way" onclick="toggleReturnDate()" required>
        <label for="one_way">One Way</label><br>
        <input type="radio" id="round_trip" name="flight_type" value="Round Trip" onclick="toggleReturnDate()">
        <label for="round_trip">Round Trip</label><br>
        <input type="radio" id="multi_city" name="flight_type" value="Multi City" onclick="toggleReturnDate()">
        <label for="multi_city">Multi City</label><br><br>

        <!-- Departure Location and Destination -->
        <label for="from">From:</label>
        <input type="text" id="from" name="from" placeholder="Departure City" required><br><br>

        <label for="to">To:</label>
        <input type="text" id="to" name="to" placeholder="Destination City" required><br><br>

        <!-- Departure Time -->
        <label for="depart_time">Departure Time:</label>
        <input type="datetime-local" id="depart_time" name="depart_time" required><br><br>

        <!-- Return Date (Visible only for Round Trip) -->
        <div id="return_date_container" style="display: none;">
            <label for="return_date">Return Date:</label>
            <input type="datetime-local" id="return_date" name="return_date"><br><br>
        </div>

        <!-- Search Button -->
        <button type="submit">Search Flight</button>
    </form>
</body>
</html>
