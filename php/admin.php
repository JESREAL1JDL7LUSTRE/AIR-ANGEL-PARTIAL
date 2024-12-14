<?php
ob_start();  // Start output buffering to ensure no output before header()
session_start();
include 'db.php';

// Check if the admin is logged in
if (!isset($_SESSION['Account_Email']) && $_SESSION['Is_Admin'] != 1) {
    header('Location: signin.php');
    exit;
}
// Queries and their labels
$queries = [
    'Total Users' => 'SELECT COUNT(*) AS count FROM Account WHERE Is_Admin = 0',
    'Total Admins' => 'SELECT COUNT(*) AS count FROM Account WHERE Is_Admin = 1',
    'Total Flights' => 'SELECT COUNT(*) AS count FROM Available_Flights',
    'Total Reservations' => 'SELECT COUNT(*) AS count FROM Reservation',
    'Total Employees' => 'SELECT COUNT(*) AS count FROM Employees',
];

// Execute queries and store results
$counts = [];
foreach ($queries as $label => $sql) {
    $result = $conn->query($sql);
    if ($result) {                                                                                                      
        $counts[$label] = $result->fetch_assoc()['count'];
    } else {
        $counts[$label] = 'Error'; // Handle query error gracefully
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/ANGEL/styles/admin.css">
</head>
<body>

<header>
        <div class="logo-container">
            <img src="/ANGEL/assets/images/logo.png" alt="AirAngel Logo" id="logo-img">
            <h1 style= "font-size: 25px;">Air Angel</h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="admin.php">Home</a></li>
            </ul>
        </nav>
        <nav class="logout">
            <ul>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </nav>
</header>
    <main>
        <header>
        <h1 style="font-family: 'Source Serif Pro', serif; font-size: 60px; margin-bottom: 10px; color:rgb(255, 255, 255);">Welcome Admin!</h1>
        <p style="font-family: 'Source Serif Pro', serif; font-size: 20px; margin: 0px; color:rgb(233, 146, 25);">You can manage your things from here</p>
        </header>
            
        <div class="cards-container">
            <div class="card available-flights" onclick="location.href='see_flights.php';">
            <div class="card-icon">✈️</div>
            <div class="card-title">Available Flights</div>
            <div class="card-description">
                Total: <?php echo htmlspecialchars($counts['Total Flights']); ?>
            </div>
            <a href="see_flights.php" class="button">View Details</a>
            </div>
            
            <div class="card total-reservations" onclick="location.href='see_reservations.php';">
            <div class="card-icon">📄</div>
            <div class="card-title">Available Reservations</div>
            <div class="card-description">
                Total: <?php echo htmlspecialchars($counts['Total Reservations']); ?>
            </div>
            <a href="see_reservations.php" class="button">View Details</a>
            </div>
            
            <div class="card employees" onclick="location.href='employees.php';">
            <div class="card-icon">👥</div>
            <div class="card-title">Employees</div>
            <div class="card-description">
                Total: <?php echo htmlspecialchars($counts['Total Employees']); ?>
            </div>
            <a href="employees.php" class="button">View Details</a>
            </div>
            
            <div class="card users" onclick="location.href='admin_see_accounts.php';">
            <div class="card-icon">👤</div>
            <div class="card-title">Users</div>
            <div class="card-description">
                Total: <?php echo htmlspecialchars($counts['Total Users']); ?>
            </div>
            <a href="admin_see_accounts.php" class="button">View Details</a>
            </div>
            
            <div class="card add-admins" onclick="location.href='add_admins.php';">
            <div class="card-icon">🛡️</div>
            <div class="card-title">Admins</div>
            <div class="card-description">
                Total: <?php echo htmlspecialchars($counts['Total Admins']); ?>
            </div>
            <a href="add_admins.php" class="button">View Details</a>
            </div>
            
            <div class="card add-ons" onclick="location.href='admin_add_ad_ons.php';">
            <div class="card-icon">🛠️</div>
            <div class="card-title">Add-ons</div>
            <a href="admin_add_ad_ons.php" class="button">Manage</a>
            </div>
            
            <div class="card add-flight" onclick="location.href='admin_add_flights.php';">
            <div class="card-icon">🛫</div>
            <div class="card-title">Add Flight</div>
            <a href="admin_add_flights.php" class="button">Add Now</a>
            </div>
            
            <div class="card assign-employees" onclick="location.href='employee_assign.php';">
            <div class="card-icon">🔧</div>
            <div class="card-title">Assign Employees</div>
            <a href="employee_assign.php" class="button">Assign</a>
            </div>
        </div>
    </main>
</body>
</html>