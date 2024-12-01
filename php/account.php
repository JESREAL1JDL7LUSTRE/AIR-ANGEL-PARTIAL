<?php
session_start();
include 'db.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Get logged-in user email
$user_email = $_SESSION['email'];

// Fetch user account information from the database
$sql_user_info = "SELECT * FROM Account WHERE Account_Email = ?";
$stmt = $conn->prepare($sql_user_info);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user_result = $stmt->get_result();

// Fetch user's booked flights
$sql_booked_flights = "SELECT * FROM Flight_to_Reservation_to_Passenger WHERE FRP_Number_ID = ?";  // Assuming there's a Bookings table
$stmt = $conn->prepare($sql_booked_flights);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$flights_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="account.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <h1>Account</h1>

        <ul class="nav-links">
            <li><a href="logout.php">Logout</a></li>
            <li><a href="account.php">Account</a></li>
        </ul>

        <h2>Welcome User!</h2>

        <h3>User Information</h3>
        <table class="user-info">
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Username</th>
            </tr>

            <?php if ($user_result && $user_result->num_rows > 0): ?>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Account_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_Last_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_First_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_Email']); ?></td>
                        <td><?php echo htmlspecialchars($row['Account_PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No user information found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <h4>Booked Flights</h4>
        <table class="flights-info">
            <tr>
                <th>Booking ID</th>
                <th>Flight Number</th>
                <th>Departure Date</th>
                <th>Arrival Date</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Amount</th>
            </tr>

            <?php if ($flights_result && $flights_result->num_rows > 0): ?>
                <?php while ($row = $flights_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Booking_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Available_Flights_Number_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Departure_Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['Arrival_Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['Origin']); ?></td>
                        <td><?php echo htmlspecialchars($row['Destination']); ?></td>
                        <td><?php echo htmlspecialchars($row['Departure_Time']); ?></td>
                        <td><?php echo htmlspecialchars($row['Arrival_Time']); ?></td>
                        <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No booked flights found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>