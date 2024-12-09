<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch baggage items from the database
$sql = "SELECT Baggage_ID, Baggage_Weight, Price FROM Baggage";
$result = $conn->query($sql);

$selectedAddons = isset($_SESSION['selected_addons']) ? $_SESSION['selected_addons'] : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Baggage Add-on</title>
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
</head>
<body>

<h1>Select Baggage Add-ons</h1>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Baggage Weight</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Baggage_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Baggage_Weight']); ?></td>
                <td><?php echo htmlspecialchars($row['Price']); ?></td>
                <td>
                <form method="POST" action="add_on.php">
                    <input type="hidden" name="addon_id" value="<?php echo $row['Baggage_ID']; ?>">
                    <input type="hidden" name="addon_name" value="<?php echo $row['Baggage_Weight']; ?>">
                    <input type="hidden" name="addon_price" value="<?php echo $row['Price']; ?>">
                    <input type="hidden" name="addon_type" value="Baggage">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>

                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No baggage available.</p>
<?php endif; ?>

<br>
<button onclick="window.location.href='add_on.php'">View All Add-ons</button>

</body>
</html>