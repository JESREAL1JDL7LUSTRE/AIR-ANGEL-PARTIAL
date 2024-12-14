<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Is_Admin']) || $_SESSION['Is_Admin'] !== 1) {
    header('Location: signin.php'); // Redirect to login page if not an admin
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values and sanitize them
    $Flight_Number = trim($_POST['Flight_Number']);
    $Departure_Date = trim($_POST['Departure_Date']);
    $Arrival_Date = trim($_POST['Arrival_Date']);
    $Origin = trim($_POST['Origin']);
    $Destination = trim($_POST['Destination']);
    $Departure_Time = trim($_POST['Departure_Time']);
    $Arrival_Time = trim($_POST['Arrival_Time']);
    $Amount = trim($_POST['Amount']);

    // Prepare the SQL query to insert the new flight
    $sql = "INSERT INTO Available_Flights (Flight_Number, Departure_Date, Arrival_Date, Origin, Destination, Departure_Time, Arrival_Time, Amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Bind the parameters to the query
    $stmt->bind_param("ssssssss", $Flight_Number, $Departure_Date, $Arrival_Date, $Origin, $Destination, $Departure_Time, $Arrival_Time, $Amount);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Flight created successfully!";
        header("Location: see_flights.php"); // Redirect to see_flights.php after successful addition
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flight</title>
    <link rel="stylesheet" href="/ANGEL/styles/cards.css">
</head>
<body>
<nav class="navbar">
    <div class="logo-container">
        <img src="/ANGEL/assets/images/logo.png" alt="AirAngel Logo" id="logo-img">
        <h1>Air Angel</h1>
    </div>
    <ul class="nav-links">
        <li><a href="admin.php">Home</a></li>
        <li><a href="see_flights.php">Flights</a></li>
        <li><a href="see_reservations.php">Reservations</a></li>
        <li><a href="admin_see_accounts.php">Users</a></li>
        <li><a href="admin_add_ad_ons.php">Add-ons</a></li>
        <li><a href="employees.php">Employees</a></li>
    </ul>
    <ul class="logout">
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <h1>Welcome Admin!</h1>
    <h2>Add Flight</h2>
    <form method="POST">
        <label>Flight Number:</label><br>
        <input type="text" name="Flight_Number" required><br>
        
        <label>Departure Date:</label><br>
        <input type="date" name="Departure_Date" required><br>
        
        <label>Arrival Date:</label><br>
        <input type="date" name="Arrival_Date" required><br>
        
        <label>Origin:</label><br>
        <input type="text" name="Origin" required><br>
        
        <label>Destination:</label><br>
        <input type="text" name="Destination" required><br>
        
        <label>Departure Time:</label><br>
        <input type="time" name="Departure_Time" required><br>
        
        <label>Arrival Time:</label><br>
        <input type="time" name="Arrival_Time" required><br>

        <label>Amount:</label><br>
        <input type="text" name="Amount" required><br>
        
        <button type="submit">Add Flight</button>
    </form>
</body>
</html>
