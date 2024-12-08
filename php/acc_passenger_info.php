<?php
ob_start();  // Start output buffering
session_start();  // Start the session
include('db.php'); // Include your database connection

// Check if flight ID is set in session before using it
if (!isset($_SESSION['selected_flight_id'])) {
    echo "Error: No flight selected. Please go back and choose a flight.";
    exit;
}

$selectedFlightID = $_SESSION['selected_flight_id'];  // Now safely get the flight ID

// Ensure the user is logged in
if (!isset($_SESSION['Account_Email']) || empty($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Initialize variables
$form_error = false;
$error_message = "";
$num_passengers = 0; // Initialize to avoid undefined variable warnings

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if number of passengers is specified
    if (!isset($_POST['numpassenger']) || empty($_POST['numpassenger'])) {
        $form_error = true;
    } else {
        $num_passengers = (int)$_POST['numpassenger'];

        // Store the number of passengers in the session
        $_SESSION['num_passengers'] = $num_passengers;

        // Initialize arrays to store passenger details
        $_SESSION['passenger_ids'] = []; // Initialize an array to store passenger IDs
        $_SESSION['passengers'] = []; // Initialize an array to store passenger details

        // Loop through the number of passengers and collect their data
        for ($i = 0; $i < $num_passengers; $i++) {
            $last_name = trim($_POST["Passenger_Last_Name_$i"] ?? '');
            $first_name = trim($_POST["Passenger_First_Name_$i"] ?? '');
            $middle_name = trim($_POST["Passenger_Middle_Name_$i"] ?? '');
            $birthday = trim($_POST["Passenger_Birthday_$i"] ?? '');
            $nationality = trim($_POST["Passenger_Nationality_$i"] ?? '');
            $email = trim($_POST["Passenger_Email_$i"] ?? '');
            $phone_number = trim($_POST["Passenger_PhoneNumber_$i"] ?? '');
            $emergency_contact = trim($_POST["Passenger_Emgergency_Contact_No_$i"] ?? '');

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $form_error = true;
                $error_message = "Invalid email format for passenger " . ($i + 1);
                break;
            }

            // Validate birthday
            if (strtotime($birthday) === false) {
                $form_error = true;
                $error_message = "Invalid date format for passenger " . ($i + 1) . "'s birthday.";
                break;
            }

            // Insert data into the database
            $stmt = $conn->prepare("
                INSERT INTO Passenger (
                    Passenger_Last_Name,
                    Passenger_First_Name,
                    Passenger_Middle_Name,
                    Passenger_Birthday,
                    Passenger_Nationality,
                    Passenger_Email,
                    Passenger_PhoneNumber,
                    Passenger_Emgergency_Contact_No
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssssss",
                $last_name,
                $first_name,
                $middle_name,
                $birthday,
                $nationality,
                $email,
                $phone_number,
                $emergency_contact
            );

            if ($stmt->execute()) {
                // Store passenger ID in session
                $passenger_id = $conn->insert_id;
                $_SESSION['passenger_ids'][] = $passenger_id;
                $_SESSION['passengers'][$i] = [
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'middle_name' => $middle_name,
                    'birthday' => $birthday,
                    'nationality' => $nationality,
                    'email' => $email,
                    'phone_number' => $phone_number,
                    'emergency_contact' => $emergency_contact
                ];
            } else {
                $form_error = true;
                $error_message = "Error saving passenger " . ($i + 1) . "'s data to the database.";
                break;
            }
        }

        // Redirect to the next page if no errors
        if (!$form_error) {
            header("Location: acc_addons.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirAngel - Airline Reservation</title>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
<header>
    <div class="header-container">
        <h1 class="site-title">AirAngel - Airline Reservation</h1>
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
    <section>
        <h2>How Many Passengers</h2>
        <form method="POST">
            <label for="numpassenger">How Many Passengers:</label>
            <input type="number" id="numpassenger" name="numpassenger" placeholder="Number of passengers" required>

            <div id="passenger-fields"></div>

            <button type="submit">Next</button>
            <button type="button" onclick="goBack()">Go Back</button>
        </form>

        <?php if ($form_error): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </section>
</main>

<script>
    document.getElementById("numpassenger").addEventListener("input", function () {
        const numpassenger = this.value;
        const container = document.getElementById("passenger-fields");
        container.innerHTML = ""; // Clear existing fields

        // Generate input fields based on number of passengers
        for (let i = 0; i < numpassenger; i++) {
            container.innerHTML += `
                <h3>Passenger ${i + 1}</h3>
                <label for="Passenger_Last_Name_${i}">Last Name:</label>
                <input type="text" id="Passenger_Last_Name_${i}" name="Passenger_Last_Name_${i}" required>
                
                <label for="Passenger_First_Name_${i}">First Name:</label>
                <input type="text" id="Passenger_First_Name_${i}" name="Passenger_First_Name_${i}" required>
                
                <label for="Passenger_Middle_Name_${i}">Middle Name:</label>
                <input type="text" id="Passenger_Middle_Name_${i}" name="Passenger_Middle_Name_${i}" required>
                
                <label for="Passenger_Birthday_${i}">Birthday:</label>
                <input type="date" id="Passenger_Birthday_${i}" name="Passenger_Birthday_${i}" required>
                
                <label for="Passenger_Nationality_${i}">Nationality:</label>
                <input type="text" id="Passenger_Nationality_${i}" name="Passenger_Nationality_${i}" required>
                
                <label for="Passenger_Email_${i}">Email:</label>
                <input type="email" id="Passenger_Email_${i}" name="Passenger_Email_${i}" required>
                
                <label for="Passenger_PhoneNumber_${i}">Phone Number:</label>
                <input type="text" id="Passenger_PhoneNumber_${i}" name="Passenger_PhoneNumber_${i}" required>
                
                <label for="Passenger_Emgergency_Contact_No_${i}">Emergency Contact No:</label>
                <input type="text" id="Passenger_Emgergency_Contact_No_${i}" name="Passenger_Emgergency_Contact_No_${i}" required>
            `;
        }
    });
</script>
</body>
</html>
