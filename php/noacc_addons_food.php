<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch food items from the database
$sql = "SELECT Food_ID, Food_Name, Price FROM Food";
$result = $conn->query($sql);

$selectedAddons = isset($_SESSION['selected_addons']) ? $_SESSION['selected_addons'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Add selected add-on to session
    $addon = [
        'ID' => $_POST['addon_id'],
        'Name' => $_POST['addon_name'],
        'Price' => $_POST['addon_price'],
        'Type' => 'Food' // Set the add-on type as Food
    ];

    // Append the selected add-on to session array
    $selectedAddons[] = $addon;

    // Save the selected add-ons back to session
    $_SESSION['selected_addons'] = $selectedAddons;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Food Add-ons</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
    <link rel="stylesheet" href="/ANGEL/styles/base.css"> <!-- base (header) -->
</head>
<body>
<header>
        <div class="header-container">
                <h1 class="site-title">AirAngel - Airline Reservation</h1>
            </div>
            <nav>
                <ul>
                <li><a href="signin.php">Sign In</a></li>
                <li><a href="signup.php">Sign Up</a></li>
                <li><a href="noacc_dashboard.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

<h1>Select Food Add-ons</h1>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Food Name</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Food_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Food_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Price']); ?></td>
                <td>
                <form method="POST" action="noacc_addons.php">
                    <input type="hidden" name="addon_id" value="<?php echo $row['Food_ID']; ?>">
                    <input type="hidden" name="addon_name" value="<?php echo $row['Food_Name']; ?>">
                    <input type="hidden" name="addon_price" value="<?php echo $row['Price']; ?>">
                    <input type="hidden" name="addon_type" value="Food">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>

                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No food available.</p>
<?php endif; ?>

<br>
<button onclick="window.location.href='noacc_addons.php'">View All Add-ons</button>

</body>
</html>
