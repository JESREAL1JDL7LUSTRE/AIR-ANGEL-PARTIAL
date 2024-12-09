<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';  // Include database connection

// Ensure the user is logged in
$is_logged_in = isset($_SESSION['Account_Email']) && !empty($_SESSION['Account_Email']);
if (!isset($_SESSION['Account_Email'])) {
    header("Location: signin.php");  // Redirect to sign-in page if not logged in
    exit;
}

// Get logged-in user email
$user_email = $_SESSION['Account_Email'];

// Handle form submission to update user info
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['Account_Email'])) {
        // Get the updated data from the form
        $first_name = $_POST['Account_First_Name'];
        $last_name = $_POST['Account_Last_Name'];
        $middle_name = $_POST['Account_Middle_Name'];
        $email = $_POST['Account_Email'];
        $phone_number = $_POST['Account_PhoneNumber'];
        $username = $_POST['Username'];
        $birthday = $_POST['Account_Birthday'];
        $sex = $_POST['Account_Sex'];
        $account_id = $_POST['Account_ID']; // Ensure Account_ID is passed for the update

        // Prepare the SQL query to update the account info
        $sql_update = "UPDATE Account 
                        SET Account_First_Name = ?, 
                            Account_Last_Name = ?, 
                            Account_Email = ?, 
                            Account_PhoneNumber = ?, 
                            Username = ?, 
                            Account_Middle_Name = ?, 
                            Account_Birthday = ?, 
                            Account_Sex = ? 
                        WHERE Account_ID = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone_number, $username, $middle_name, $birthday, $sex, $account_id);

        // Execute the update query
        if ($stmt->execute()) {
            echo "<script>alert('Account updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating account!');</script>";
        }
    }
}

// Fetch user account information from the database
$sql_user_info = "SELECT * FROM Account WHERE Account_Email = ?";
$stmt = $conn->prepare($sql_user_info);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user_result = $stmt->get_result();

// Fetch user's booked flights
$sql_booked_flights = "SELECT * FROM Flight_to_Reservation_to_Passenger WHERE FRP_Number_ID = ?";
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
    <link rel="stylesheet" href="/ANGEL/styles/account.css">
</head>
<body>
    <h1>Account</h1>

    <ul>
    <?php if (!$is_logged_in): ?>
        <li><a href="signin.php">Sign In</a></li>
        <li><a href="signup.php">Sign Up</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="user_dashboard.php">Home</a></li>
    <?php endif; ?>
    </ul>

    <h2>Welcome User!</h2>

    <h3>User Information</h3>
    <form method="POST">
        <table border="1">
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Username</th>
                <th>Birthday</th>
                <th>Sex</th>
                <th>Action</th>
            </tr>
            <?php if ($user_result && $user_result->num_rows > 0): ?>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="text" name="Account_Last_Name" value="<?php echo htmlspecialchars($row['Account_Last_Name']); ?>" required class="view-only" id="Account_Last_Name" readonly></td>
                        <td><input type="text" name="Account_First_Name" value="<?php echo htmlspecialchars($row['Account_First_Name']); ?>" required class="view-only" id="Account_First_Name" readonly></td>
                        <td><input type="text" name="Account_Middle_Name" value="<?php echo htmlspecialchars($row['Account_Middle_Name']); ?>" class="view-only" id="Account_Middle_Name" readonly></td>
                        <td><input type="email" name="Account_Email" value="<?php echo htmlspecialchars($row['Account_Email']); ?>" required class="view-only" id="Account_Email" readonly></td>
                        <td><input type="text" name="Account_PhoneNumber" value="<?php echo htmlspecialchars($row['Account_PhoneNumber']); ?>" class="view-only" id="Account_PhoneNumber" readonly></td>
                        <td><input type="text" name="Username" value="<?php echo htmlspecialchars($row['Username']); ?>" required class="view-only" id="Username" readonly></td>
                        <td><input type="date" name="Account_Birthday" value="<?php echo htmlspecialchars($row['Account_Birthday']); ?>" class="view-only" id="Account_Birthday" readonly></td>

                        <td>
                            <div class="radio-group">
                                <input type="radio" id="Male" name="Account_Sex" value="Male" <?php echo ($row['Account_Sex'] == 'Male') ? 'checked' : ''; ?> class="view-only" disabled>
                                <label for="Male">Male</label>

                                <input type="radio" id="Female" name="Account_Sex" value="Female" <?php echo ($row['Account_Sex'] == 'Female') ? 'checked' : ''; ?> class="view-only" disabled>
                                <label for="Female">Female</label>
                            </div>
                        </td>

                        <td>
                            <input type="hidden" name="Account_ID" value="<?php echo $row['Account_ID']; ?>"> <!-- Pass Account_ID for update -->
                            <button type="button" id="editButton">Edit</button>
                            <button type="submit" id="saveButton" style="display: none;">Save</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No user information found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </form>

    <h4>Booked Flights</h4>
    <table border="1">
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
                    <td><?php echo htmlspecialchars($row['FRP_Number_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Flights_Number']); ?></td>
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

    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            // Enable inputs and show the save button
            var inputs = document.querySelectorAll('.view-only');
            inputs.forEach(function(input) {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.classList.add('editable');
            });
            document.getElementById('saveButton').style.display = 'inline-block';
            document.getElementById('editButton').style.display = 'none';
        });
    </script>
</body>
</html>
