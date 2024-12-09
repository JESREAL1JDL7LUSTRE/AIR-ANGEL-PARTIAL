<?php
ob_start();  // Start output buffering
session_start();  // Start the session
include 'db.php';  // Include the database connection

// Initialize variables
$Passenger_Last_Name = $Passenger_First_Name = $Passenger_Middle_Name = $Passenger_Birthday = [];
$Passenger_Nationality = $Passenger_Email = $Passenger_PhoneNumber = $Passenger_Emgergency_Contact_No = [];

// Error flag
$form_error = false;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the number of passengers is provided
    if (!isset($_POST['numpassenger']) || empty($_POST['numpassenger'])) {
        $form_error = true;
        $error_message = "Please specify the number of passengers.";
    } else {
        $num_passengers = (int)$_POST['numpassenger'];

        // Store passenger data in session
        $_SESSION['num_passengers'] = $num_passengers;
        $_SESSION['passengers'] = [];

        // Validate and store passenger data in session
        for ($i = 0; $i < $num_passengers; $i++) {
            // Validate inputs (e.g., email, date format)
            $last_name = trim($_POST["Passenger_Last_Name_$i"] ?? '');
            $first_name = trim($_POST["Passenger_First_Name_$i"] ?? '');
            $middle_name = trim($_POST["Passenger_Middle_Name_$i"] ?? '');
            $birthday = trim($_POST["Passenger_Birthday_$i"] ?? '');
            $nationality = trim($_POST["Passenger_Nationality_$i"] ?? '');
            $email = trim($_POST["Passenger_Email_$i"] ?? '');
            $phone_number = trim($_POST["Passenger_PhoneNumber_$i"] ?? '');
            $emergency_contact = trim($_POST["Passenger_Emgergency_Contact_No_$i"] ?? '');

            // Basic validation for email and birthday
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $form_error = true;
                $error_message = "Invalid email format.";
                break;
            }

            // Simple date validation (make sure it's in the YYYY-MM-DD format)
            if (strtotime($birthday) === false) {
                $form_error = true;
                $error_message = "Invalid date format for birthday. Please use YYYY-MM-DD.";
                break;
            }

            // Store validated passenger data in session
            $_SESSION['passengers'][$i] = [
                'Passenger_Last_Name' => $last_name,
                'Passenger_First_Name' => $first_name,
                'Passenger_Middle_Name' => $middle_name,
                'Passenger_Birthday' => $birthday,
                'Passenger_Nationality' => $nationality,
                'Passenger_Email' => $email,
                'Passenger_PhoneNumber' => $phone_number,
                'Passenger_Emgergency_Contact_No' => $emergency_contact
            ];

            // Save to database (adjust query for your schema)
            $sql = "INSERT INTO Passenger (Passenger_Last_Name, Passenger_First_Name, Passenger_Middle_Name, Passenger_Birthday, Passenger_Nationality, Passenger_Email, Passenger_PhoneNumber, Passenger_Emgergency_Contact_No)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssss",
                $_SESSION['passengers'][$i]['Passenger_Last_Name'],
                $_SESSION['passengers'][$i]['Passenger_First_Name'],
                $_SESSION['passengers'][$i]['Passenger_Middle_Name'],
                $_SESSION['passengers'][$i]['Passenger_Birthday'],
                $_SESSION['passengers'][$i]['Passenger_Nationality'],
                $_SESSION['passengers'][$i]['Passenger_Email'],
                $_SESSION['passengers'][$i]['Passenger_PhoneNumber'],
                $_SESSION['passengers'][$i]['Passenger_Emgergency_Contact_No']
            );
            if (!$stmt->execute()) {
                $form_error = true;
                $error_message = "Error saving passenger data to the database: " . $stmt->error;
                break;
            }
        }

        // Redirect only if no errors occurred
        if (!$form_error) {
            header("Location: add_on.php");
            exit();
        } else {
            // Show error message to user (e.g., use $error_message to display on the form)
            echo "<p>Error: $error_message</p>";
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
</head>
<body>
    <header>
        <h1>AirAngel - Airline Reservation</h1>
    </header>
    <main>
        <section>
            <h2>How Many Passengers</h2>
            <form method="POST">
                <label for="numpassenger">How Many Passengers:</label>
                <input type="number" id="numpassenger" name="numpassenger" placeholder="Number of passengers" required>

                <div id="passenger-fields"></div>

                <button type="submit">Next</button>
            </form>
        </section>
    </main>
    <script>
        document.getElementById("numpassenger").addEventListener("input", function() {
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
