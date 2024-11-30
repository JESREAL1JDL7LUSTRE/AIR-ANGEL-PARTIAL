<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "airangel";

$conn = new mysqli($host, $user, $pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
