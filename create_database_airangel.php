<?php  
// Database credentials
$host = "localhost:8111";
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
$hashed_password = password_hash('thebest', PASSWORD_DEFAULT);
// Create tables
$sql_create_tables = [
    "CREATE TABLE IF NOT EXISTS Account (
        Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Account_Last_Name VARCHAR(50) NOT NULL,
        Account_First_Name VARCHAR(50) NOT NULL,
        Account_Email VARCHAR(50) NOT NULL,
        Account_PhoneNumber INT(11) NOT NULL,
        Username VARCHAR(50) NOT NULL,
        Password VARCHAR(500) NOT NULL,
        Is_Admin INT(1) NOT NULL DEFAULT 0
    )",
    
    "INSERT INTO Account (Account_ID, Account_Last_Name, Account_First_Name, Account_Email, Account_PhoneNumber, Username, Password, Is_Admin) 
    VALUES (1, 'Lustre', 'Jesreal', 'jsrl.lustre@admin.com', '09750000000', 'BOSS', '$hashed_password', 1)",

    "CREATE TABLE IF NOT EXISTS Saved_Detail (
        Saved_Detail_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Saved_Detail_LastName VARCHAR(50) NOT NULL,
        Saved_Detail_FirstName VARCHAR(50) NOT NULL,
        Saved_Detail_Birthday DATE NOT NULL,
        Saved_Detail_Phone_Number VARCHAR(11) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Payment_Method (
        Payment_Method_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Payment_Method_Name VARCHAR(20) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Account_to_Passenger (
        Account_to_Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
    "CREATE TABLE IF NOT EXISTS Reservation_to_account (
        Reservation_to_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
    "CREATE TABLE IF NOT EXISTS Saved_Detail_in_Account (
        SD_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
    "CREATE TABLE IF NOT EXISTS Payment (
        Payment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Payment_Amount DECIMAL(10,2) NOT NULL,
        Payment_Date DATE NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Passenger (
        Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Passenger_Last_Name VARCHAR(50) NOT NULL,
        Passenger_First_Name VARCHAR(50) NOT NULL,
        Passenger_Middle_Name VARCHAR(50) NOT NULL,
        Passenger_Birthday DATE NOT NULL,
        Passenger_Nationality VARCHAR(50) NOT NULL,
        Passenger_Email VARCHAR(50) NOT NULL,
        Passenger_PhoneNumber VARCHAR(11) NOT NULL,
        Passenger_Emgergency_Contact_No VARCHAR(11) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Reservation (
        Reservation_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Booking_date DATE NOT NULL

    )",
    "CREATE TABLE IF NOT EXISTS Baggage (
        Baggage_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Baggage_Weight VARCHAR(20) NOT NULL,
        Price DECIMAL(10,2) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Available_Flights (
        Available_Flights_Number_ID INT(15) AUTO_INCREMENT PRIMARY KEY,
        Departure_Date DATE NOT NULL,
        Arrival_Date DATE NOT NULL,
        Origin VARCHAR(50) NOT NULL,
        Destination VARCHAR(50) NOT NULL,
        Departure_Time TIME NOT NULL,
        Arrival_Time TIME NOT NULL,
        Amount DECIMAL(10,2) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Flight_to_Reservation_to_Passenger (
        FRP_Number_ID INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
    "CREATE TABLE IF NOT EXISTS Add_on (
        Add_on_ID INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
    "CREATE TABLE IF NOT EXISTS Employee_Assignment (
        Employee_Assignment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Employee_Role VARCHAR(50) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Department (
        Department_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Department_Name VARCHAR(30) NOT NULL,
        Department_Type VARCHAR(20) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Employees (
        Employee_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Department VARCHAR(30) NOT NULL,
        Employee_Last_Name VARCHAR(50) NOT NULL,
        Employee_First_Name VARCHAR(50) NOT NULL,
        Employee_Middle_Name VARCHAR(50) NOT NULL,
        Employee_Birthday DATE NOT NULL,
        Employee_Nationality VARCHAR(30) NOT NULL,
        Employee_Sex VARCHAR(10) NOT NULL,
        Employee_Address VARCHAR(50) NOT NULL,
        Employee_PhoneNumber VARCHAR(11) NOT NULL,
        Employee_Emergency_Contact_No VARCHAR(11) NOT NULL,
        Employee_Salary DECIMAL(50) NOT NULL,
        Employee_Health_Insurance VARCHAR(50) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Seat_Selector (
        Seat_Selector_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Seat_Selector_Number VARCHAR(10) NOT NULL,
        Price DECIMAL(10,2) NOT NULL,
        Seat_Type VARCHAR(20) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Food (
        Food_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Food_Name VARCHAR(30) NOT NULL,
        Price DECIMAL(10,2) NOT NULL,
        Seat_Type VARCHAR(20) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Reservation_to_Passenger (
        Reservation_to_Passenger INT(10) AUTO_INCREMENT PRIMARY KEY
    )",
];

foreach ($sql_create_tables as $sql) {
    // Updated regular expression to capture table names, with or without backticks
    preg_match('/CREATE TABLE IF NOT EXISTS `?([a-zA-Z0-9_]+)`?/i', $sql, $matches);
    $table_name = $matches[1] ?? 'Unknown Table';

    if ($conn->query($sql) === TRUE) {
        echo "Table '$table_name' created successfully.<br>";
    } else {
        echo "Error creating table '$table_name': " . $conn->error . "<br>";
    }
}

// Add foreign key columns and constraints
$sql_add_foreign_keys = [
    // Account Table
    "ALTER TABLE Account ADD COLUMN SD_Account_ID_FK INT(10)",
    "ALTER TABLE Account ADD COLUMN Account_to_Passenger_ID_FK INT(10)",
    "ALTER TABLE Account ADD COLUMN Reservation_to_Account_ID_FK INT(10)",
    "ALTER TABLE Account ADD INDEX (SD_Account_ID_FK)",
    "ALTER TABLE Account ADD INDEX (Account_to_Passenger_ID_FK)",
    "ALTER TABLE Account ADD INDEX (Reservation_to_Account_ID_FK)",
    "ALTER TABLE Account ADD FOREIGN KEY (SD_Account_ID_FK) REFERENCES Saved_Detail(Saved_Detail_ID)",
    "ALTER TABLE Account ADD FOREIGN KEY (Account_to_Passenger_ID_FK) REFERENCES Account_to_Passenger(Account_to_Passenger_ID)",
    "ALTER TABLE Account ADD FOREIGN KEY (Reservation_to_Account_ID_FK) REFERENCES Reservation_to_account(Reservation_to_Account_ID)",

    // Account_to_Passenger Table
    "ALTER TABLE Account_to_Passenger ADD COLUMN Account_ID_FK INT(10)",
    "ALTER TABLE Account_to_Passenger ADD COLUMN Passenger_ID_FK INT(10)",
    "ALTER TABLE Account_to_Passenger ADD INDEX (Account_ID_FK)",
    "ALTER TABLE Account_to_Passenger ADD INDEX (Passenger_ID_FK)",
    "ALTER TABLE Account_to_Passenger ADD FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID)",
    "ALTER TABLE Account_to_Passenger ADD FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID)",

    // Reservation_to_account Table
    "ALTER TABLE Reservation_to_account ADD COLUMN Reservation_ID_FK INT(10)",
    "ALTER TABLE Reservation_to_account ADD COLUMN Account_ID_FK INT(10)",
    "ALTER TABLE Reservation_to_account ADD INDEX (Reservation_ID_FK)",
    "ALTER TABLE Reservation_to_account ADD INDEX (Account_ID_FK)",
    "ALTER TABLE Reservation_to_account ADD FOREIGN KEY (Reservation_ID_FK) REFERENCES Reservation(Reservation_ID)",
    "ALTER TABLE Reservation_to_account ADD FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID)",

    // Saved_Detail_in_Account Table
    "ALTER TABLE Saved_Detail_in_Account ADD COLUMN Account_ID_FK INT(10)",
    "ALTER TABLE Saved_Detail_in_Account ADD COLUMN Saved_Detail_ID_FK INT(10)",
    "ALTER TABLE Saved_Detail_in_Account ADD INDEX (Account_ID_FK)",
    "ALTER TABLE Saved_Detail_in_Account ADD INDEX (Saved_Detail_ID_FK)",
    "ALTER TABLE Saved_Detail_in_Account ADD FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID)",
    "ALTER TABLE Saved_Detail_in_Account ADD FOREIGN KEY (Saved_Detail_ID_FK) REFERENCES Saved_Detail(Saved_Detail_ID)",

    // Payment Table
    "ALTER TABLE Payment ADD COLUMN Payment_Method_ID_FK INT(10)",
    "ALTER TABLE Payment ADD INDEX (Payment_Method_ID_FK)",
    "ALTER TABLE Payment ADD FOREIGN KEY (Payment_Method_ID_FK) REFERENCES Payment_Method(Payment_Method_ID)",

    // Reservation Table
    "ALTER TABLE Reservation ADD COLUMN Payment_ID_FK INT(10)",
    "ALTER TABLE Reservation ADD COLUMN Employee_Assignment_ID_FK INT(10)",
    "ALTER TABLE Reservation ADD INDEX (Payment_ID_FK)",
    "ALTER TABLE Reservation ADD INDEX (Employee_Assignment_ID_FK)",
    "ALTER TABLE Reservation ADD FOREIGN KEY (Payment_ID_FK) REFERENCES Payment(Payment_ID)",
    "ALTER TABLE Reservation ADD FOREIGN KEY (Employee_Assignment_ID_FK) REFERENCES Employee_Assignment(Employee_Assignment_ID)",

    // Baggage Table
    "ALTER TABLE Baggage ADD COLUMN Available_Flights_Number_ID_FK INT(15)",
    "ALTER TABLE Baggage ADD INDEX (Available_Flights_Number_ID_FK)",
    "ALTER TABLE Baggage ADD FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)",
    
    // Employee_Assignment Table
    "ALTER TABLE Employee_Assignment ADD COLUMN Employee_ID_FK INT(10)",
    "ALTER TABLE Employee_Assignment ADD COLUMN Department_ID_FK INT(10)",
    "ALTER TABLE Employee_Assignment ADD COLUMN Available_Flights_Number_ID_FK INT(15)",
    "ALTER TABLE Employee_Assignment ADD INDEX (Employee_ID_FK)",
    "ALTER TABLE Employee_Assignment ADD INDEX (Department_ID_FK)",
    "ALTER TABLE Employee_Assignment ADD INDEX (Available_Flights_Number_ID_FK)",
    "ALTER TABLE Employee_Assignment ADD FOREIGN KEY (Employee_ID_FK) REFERENCES Employees(Employee_ID)",
    "ALTER TABLE Employee_Assignment ADD FOREIGN KEY (Department_ID_FK) REFERENCES Department(Department_ID)",
    "ALTER TABLE Employee_Assignment ADD FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)",

    // Add_on Table
    "ALTER TABLE Add_on ADD COLUMN FRP_Number_ID_FK INT(15)",
    "ALTER TABLE Add_on ADD COLUMN Seat_Selector_ID_FK INT(10)",
    "ALTER TABLE Add_on ADD COLUMN Food_ID_FK INT(10)",
    "ALTER TABLE Add_on ADD COLUMN Baggage_ID_FK INT(10)",
    "ALTER TABLE Add_on ADD INDEX (FRP_Number_ID_FK)",
    "ALTER TABLE Add_on ADD INDEX (Seat_Selector_ID_FK)",
    "ALTER TABLE Add_on ADD INDEX (Food_ID_FK)",
    "ALTER TABLE Add_on ADD INDEX (Baggage_ID_FK)",
    "ALTER TABLE Add_on ADD FOREIGN KEY (FRP_Number_ID_FK) REFERENCES Flight_to_Reservation_to_Passenger(FRP_Number_ID)",
    "ALTER TABLE Add_on ADD FOREIGN KEY (Seat_Selector_ID_FK) REFERENCES Seat_Selector(Seat_Selector_ID)",
    "ALTER TABLE Add_on ADD FOREIGN KEY (Food_ID_FK) REFERENCES Food(Food_ID)",
    "ALTER TABLE Add_on ADD FOREIGN KEY (Baggage_ID_FK) REFERENCES Baggage(Baggage_ID)",

    // Seat_Selector Table
    "ALTER TABLE Seat_Selector ADD COLUMN Passenger_ID_FK INT(10)",
    "ALTER TABLE Seat_Selector ADD COLUMN Available_Flights_Number_ID_FK INT(15)",
    "ALTER TABLE Seat_Selector ADD INDEX (Passenger_ID_FK)",
    "ALTER TABLE Seat_Selector ADD INDEX (Available_Flights_Number_ID_FK)",
    "ALTER TABLE Seat_Selector ADD FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID)",
    "ALTER TABLE Seat_Selector ADD FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)",

    "ALTER TABLE Food ADD COLUMN Available_Flights_Number_FK INT(15)",
    "ALTER TABLE Food ADD INDEX (Available_Flights_Number_FK)",
    "ALTER TABLE Food ADD FOREIGN KEY (Available_Flights_Number_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)",

    "ALTER TABLE Reservation_to_Passenger ADD COLUMN Passenger_ID_FK INT(10)",
    "ALTER TABLE Reservation_to_Passenger ADD COLUMN Reservation_ID_FK INT(10)",
    "ALTER TABLE Reservation_to_Passenger ADD INDEX (Passenger_ID_FK)",
    "ALTER TABLE Reservation_to_Passenger ADD INDEX (Reservation_ID_FK)",
    "ALTER TABLE Reservation_to_Passenger ADD FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID)",
    "ALTER TABLE Reservation_to_Passenger ADD FOREIGN KEY (Reservation_ID_FK) REFERENCES Reservation(Reservation_ID)",
    
    // Flight_to_Reservation_to_Passenger Table
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD COLUMN Flight_to_Reservation_ID_FK INT(10)",
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD COLUMN Available_Flights_Number_ID_FK INT(15)",
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD INDEX (Flight_to_Reservation_ID_FK)",
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD INDEX (Available_Flights_Number_ID_FK)",
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD FOREIGN KEY (Flight_to_Reservation_ID_FK) REFERENCES Flight_to_Reservation_to_Passenger(Flight_to_Reservation_to_Passenger_ID)",
    "ALTER TABLE Flight_to_Reservation_to_Passenger ADD FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights (Available_Flights_Number_ID)",

];

foreach ($sql_add_foreign_keys as $sql) {
    echo "Processing SQL: $sql<br>";  // Debugging line

    // Regular expression to capture the referenced table name
    preg_match('/FOREIGN KEY\s+\([^)]+\)\s+REFERENCES\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $matches);

    $table_name = isset($matches[1]) ? $matches[1] : 'Unknown Table';

    if ($conn->query($sql) === TRUE) {
        echo "Foreign key added to table '$table_name' successfully.<br>";
    } else {
        echo "Error adding foreign key to table '$table_name': " . $conn->error . "<br>";
    }
}

$conn->close();

echo "Database and table setup completed.";
?>