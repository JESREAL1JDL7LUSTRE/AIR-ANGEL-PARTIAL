<?php
// Database credentials
$host = "localhost";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$db_name = "airangel";
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Database '$db_name' created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($db_name);

// Create table
$sql_create_table_Account = "
CREATE TABLE IF NOT EXISTS Account (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved_Detail_in_Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account_to_Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation_to_account(Reservation_to_Account_ID),
)";
$sql_create_table_Saved_Detail = "
CREATE TABLE IF NOT EXISTS Saved_Detail (
    Saved_Detail_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Saved_Detail_LastName VARCHAR(50) NOT NULL,
    Saved_Detail_FirstName VARCHAR(50) NOT NULL,
    Saved_Detail_Birthday DATE NOT NULL,
    Saved_Detail_Phone_Number VARCHAR(11) NOT NULL,
)";
$sql_create_table_Payment_Method= "
CREATE TABLE IF NOT EXISTS Payment_Method (
    Payment_Method_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Payment_Method_Name VARCHAR(20) NOT NULL,
)";
$sql_create_table_Account_to_Passenger = "
CREATE TABLE IF NOT EXISTS Account_to_Passenger (
    Account_to_Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_ID_FK int(10) FOREIGN KEY REFERENCES Account(Account_ID),
    Passenger_ID_FK int(10) FOREIGN KEY REFERENCES Passenger(Passenger_ID),
)";
$sql_create_table_Reservation_to_account = "
CREATE TABLE IF NOT EXISTS Reservation_to_account (
    Reservation_to_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Reservation_ID_FK int(10) FOREIGN KEY REFERENCES Account to Reservation(Reservation_to_Passenger_ID),
    Account_ID_FK int(10) FOREIGN KEY REFERENCES Account(Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_Last_Name VARCHAR(50) NOT NULL,
    Account_First_Name VARCHAR(50) NOT NULL,
    Account_Email VARCHAR(50) NOT NULL,
    Account_PhoneNumber VARCHAR(50) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    SD_Account_ID int(10) FOREIGN KEY REFERENCESL Saved Detail in Account(SD_Account_ID),
    Account_to_Passenger_ID int(10) FOREIGN KEY REFERENCES Account to Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID int(10) FOREIGN KEY REFERENCES Reservation to account(Reservation_to_Account_ID),
)";

if ($conn->query($sql_create_table) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Close connection
$conn->close();

echo "Database and table setup completed.";
?>
