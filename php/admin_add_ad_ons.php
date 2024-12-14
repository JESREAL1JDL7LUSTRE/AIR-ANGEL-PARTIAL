<?php
ob_start();
session_start();
include 'db.php'; // Include your database connection

// Handle Add-on Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addOnType = $_POST['addOnType'] ?? '';
    $price = null;

    // Get price based on the add-on type
    if ($addOnType === 'Baggage') {
        $price = $_POST['baggagePrice'] ?? null;
    } elseif ($addOnType === 'Food') {
        $price = $_POST['foodPrice'] ?? null;
    } elseif ($addOnType === 'SeatSelector') {
        $price = $_POST['seatSelectorPrice'] ?? null;
    }

    // Validate price (ensure it's numeric)

        // Handle submission based on add-on type
        if ($addOnType === 'Baggage' && isset($_POST['baggageWeight'])) {
            $baggageWeight = $_POST['baggageWeight'];
            $stmt = $conn->prepare("INSERT INTO Baggage (Baggage_Weight, Price) VALUES (?, ?)");
            $stmt->bind_param("si", $baggageWeight, $price);
        } elseif ($addOnType === 'Food' && isset($_POST['foodName'])) {
            $foodName = $_POST['foodName'];
            $stmt = $conn->prepare("INSERT INTO Food (Food_Name, Price) VALUES (?, ?)");
            $stmt->bind_param("si", $foodName, $price);
        } elseif ($addOnType === 'SeatSelector' && isset($_POST['seatSelectorNumber'])) {
            $seatNumber = $_POST['seatSelectorNumber'];
            $stmt = $conn->prepare("INSERT INTO Seat_Selector (Seat_Selector_Number, Price) VALUES (?, ?)");
            $stmt->bind_param("si", $seatNumber, $price);
        }

        // Execute and check for success
        if (isset($stmt) && $stmt->execute()) {
            echo "<script>alert('Add-on added successfully!'); window.location.href = 'admin_add_ad_ons.php';</script>";
        }
    
}

// Fetch Add-ons for Display
$selectedType = $_GET['type'] ?? 'Baggage';
switch ($selectedType) {
    case 'Food':
        $sql = "SELECT Food_ID AS ID, Food_Name AS Name, Price FROM Food";
        break;
    case 'SeatSelector':
        $sql = "SELECT Seat_Selector_ID AS ID, Seat_Selector_Number AS Name, Price FROM Seat_Selector";
        break;
    default:
        $sql = "SELECT Baggage_ID AS ID, Baggage_Weight AS Name, Price FROM Baggage";
        $selectedType = 'Baggage';
}
$result = $conn->query($sql);

// Handle Save
if (isset($_POST['save'])) {
    $id = $_POST['ID'];
    $name = $_POST['Name'];
    $price = $_POST['Price'];

    // Validate price (ensure it's numeric)
    if (empty($price) || !is_numeric($price)) {
        echo "<script>alert('Price must be a valid number.');</script>";
    } else {
        // Update the record in the database based on selected add-on type
        if ($selectedType === 'Food') {
            $stmt = $conn->prepare("UPDATE Food SET Food_Name = ?, Price = ? WHERE Food_ID = ?");
            $stmt->bind_param("ssi", $name, $price, $id);
        } elseif ($selectedType === 'SeatSelector') {
            $stmt = $conn->prepare("UPDATE Seat_Selector SET Seat_Selector_Number = ?, Price = ? WHERE Seat_Selector_ID = ?");
            $stmt->bind_param("ssi", $name, $price, $id);
        } else {
            $stmt = $conn->prepare("UPDATE Baggage SET Baggage_Weight = ?, Price = ? WHERE Baggage_ID = ?");
            $stmt->bind_param("ssi", $name, $price, $id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully.'); window.location.href = 'admin_add_ad_ons.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
        }
    }
}

// Handle Delete
if (isset($_POST['delete'])) {
    $id = $_POST['ID'];

    // Delete the record from the database based on selected add-on type
    if ($selectedType === 'Food') {
        $stmt = $conn->prepare("DELETE FROM Food WHERE Food_ID = ?");
    } elseif ($selectedType === 'SeatSelector') {
        $stmt = $conn->prepare("DELETE FROM Seat_Selector WHERE Seat_Selector_ID = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM Baggage WHERE Baggage_ID = ?");
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully.'); window.location.href = 'admin_add_ad_ons.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ANGEL/styles/cards.css">
    <title>Add Add-ons</title>
    <script>
        function toggleAddOnForm() {
            const form = document.getElementById('adminSignupForm');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    }
        // Function to toggle visibility of specific input containers
        function toggleAddOnType(type) {
            // Hide all containers initially
            document.getElementById("baggageWeightContainer").style.display = "none";
            document.getElementById("baggagePriceContainer").style.display = "none";
            document.getElementById("foodNameContainer").style.display = "none";
            document.getElementById("foodPriceContainer").style.display = "none";
            document.getElementById("seatSelectorNumberContainer").style.display = "none";
            document.getElementById("seatSelectorPriceContainer").style.display = "none";

            // Show the relevant fields based on the type
            if (type === "baggage") {
                document.getElementById("baggageWeightContainer").style.display = "block";
                document.getElementById("baggagePriceContainer").style.display = "block";
            } else if (type === "food") {
                document.getElementById("foodNameContainer").style.display = "block";
                document.getElementById("foodPriceContainer").style.display = "block";
            } else if (type === "seatSelector") {
                document.getElementById("seatSelectorNumberContainer").style.display = "block";
                document.getElementById("seatSelectorPriceContainer").style.display = "block";
            }
        }
        function changeAddOnType(type) {
            window.location.href = "?type=" + type;
        }
            // Update the table column header based on the selected add-on type
        function changeAddOnType(type) {
            // Change the URL to reflect the selected add-on type
            window.location.href = "?type=" + type;
        }
    </script>
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
<div id="adminSignupForm" style="display:none;">
        <h2 style="text-align: center;">Create New Add-ons</h2>
        <form method="POST" action="" style="text-align: center;">
            <legend>Select Add-on Type:</legend>
            <div class="radio-group">
            <input type="radio" id="baggage" name="addOnType" value="Baggage" onclick="toggleAddOnType('baggage')" required>
            <label for="baggage">Baggage</label>
            <input type="radio" id="food" name="addOnType" value="Food" onclick="toggleAddOnType('food')" required>
            <label for="food">Food</label>
            <input type="radio" id="seatSelector" name="addOnType" value="SeatSelector" onclick="toggleAddOnType('seatSelector')" required>
            <label for="seatSelector">Seat Selector</label>
            </div>

            <!-- Add-on specific fields here -->
            <div id="baggageWeightContainer" style="display:none;">
            <label for="baggageWeight">Baggage Weight:</label>
            <input type="text" id="baggageWeight" name="baggageWeight">
            </div>
            <div id="baggagePriceContainer" style="display:none;">
            <label for="baggagePrice">Price:</label>
            <input type="text" id="baggagePrice" name="baggagePrice">
            </div>

            <div id="foodNameContainer" style="display:none;">
            <label for="foodName">Food Name:</label>
            <input type="text" id="foodName" name="foodName">
            </div>
            <div id="foodPriceContainer" style="display:none;">
            <label for="foodPrice">Price:</label>
            <input type="text" id="foodPrice" name="foodPrice">
            </div>

            <div id="seatSelectorNumberContainer" style="display:none;">
            <label for="seatSelectorNumber">Seat Selector Number:</label>
            <input type="text" id="seatSelectorNumber" name="seatSelectorNumber">
            </div>
            <div id="seatSelectorPriceContainer" style="display:none;">
            <label for="seatSelectorPrice">Price:</label>
            <input type="text" id="seatSelectorPrice" name="seatSelectorPrice">
            </div>

            <button type="submit" style="background-color: #233D2C; color: white; margin-top: 20px;">Add Add-on</button>
        </form>
    </div>                                                                         
</div>
<div class="actions">
    <h1>All Add-ons</h1>
    <a class="add-button" onclick="toggleAddOnForm()">Add Add-ons</a>
</div>

<div style="text-align: center;">
    <legend>Select Add-on Type:</legend>
    <div class="radio-group">
        <input type="radio" id="baggageView" name="viewAddOnType" value="Baggage" 
            onclick="changeAddOnType('Baggage')" <?= $selectedType === 'Baggage' ? 'checked' : '' ?>>
        <label for="baggageView">Baggage</label>

        <input type="radio" id="foodView" name="viewAddOnType" value="Food" 
            onclick="changeAddOnType('Food')" <?= $selectedType === 'Food' ? 'checked' : '' ?>>
        <label for="foodView">Food</label>

        <input type="radio" id="seatSelectorView" name="viewAddOnType" value="SeatSelector" 
            onclick="changeAddOnType('SeatSelector')" <?= $selectedType === 'SeatSelector' ? 'checked' : '' ?>>
        <label for="seatSelectorView">Seat Selector</label>
    </div>
</div>


<table border="1">
    <tr>
        <th>ID</th>
        <th>
            <?php
                if ($selectedType === 'Food') {
                    echo "Food Name";
                } elseif ($selectedType === 'SeatSelector') {
                    echo "Seat Selector Number";
                } else {
                    echo "Baggage Weight";
                }
            ?>
        </th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="">
                    <td><?php echo htmlspecialchars($row['ID']); ?></td>
                    <td>
                        <input type="text" name="Name" value="<?php echo htmlspecialchars($row['Name']); ?>" readonly>
                    </td>
                    <td>
                        <input type="text" name="Price" value="<?php echo htmlspecialchars($row['Price']); ?>" readonly>
                    </td>
                    <td>
                        <button type="button" class="editButton" onclick="editRow(this)">Edit</button>
                        <button type="submit" name="save" style="display:none;">Save</button>
                        <button type="submit" name="delete" style="display:inline;">Delete</button>
                    </td>
                    <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
                </form>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No records found.</td>
        </tr>
    <?php endif; ?>
</table>

<script>
    function editRow(button) {
        // Enable input fields for editing
        var row = button.closest('tr');
        var inputs = row.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.removeAttribute('readonly');
        });
        row.querySelector('button[name="save"]').style.display = 'inline-block';
        row.querySelector('button[name="edit"]').style.display = 'none';
    }
</script>

</body>
</html>
