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

// Create tables
$sql_create_tables = [
    "CREATE TABLE IF NOT EXISTS Account (
        Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Account_Last_Name VARCHAR(50) NOT NULL,
        Account_First_Name VARCHAR(50) NOT NULL,
        Account_Email VARCHAR(50) NOT NULL,
        Account_PhoneNumber VARCHAR(50) NOT NULL,
        Username VARCHAR(50) NOT NULL,
        Password VARCHAR(50) NOT NULL,
        SD_Account_ID_FK INT(10),
        Account_to_Passenger_ID_FK INT(10),
        Reservation_to_Account_ID_FK INT(10),
        FOREIGN KEY (SD_Account_ID_FK) REFERENCES Saved_Detail(Saved_Detail_ID),
        FOREIGN KEY (Account_to_Passenger_ID_FK) REFERENCES Account_to_Passenger(Account_to_Passenger_ID),
        FOREIGN KEY (Reservation_to_Account_ID_FK) REFERENCES Reservation_to_account(Reservation_to_Account_ID)
    )",

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
        Account_to_Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Account_ID_FK INT(10),
        Passenger_ID_FK INT(10),
        FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID),
        FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Reservation_to_account (
        Reservation_to_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Reservation_ID_FK INT(10),
        Account_ID_FK INT(10),
        FOREIGN KEY (Reservation_ID_FK) REFERENCES Reservation(Reservation_ID),
        FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Saved_Detail_in_Account (
        SD_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Account_ID_FK INT(10),
        Saved_Detail_ID_FK INT(10),
        FOREIGN KEY (Account_ID_FK) REFERENCES Account(Account_ID),
        FOREIGN KEY (Saved_Detail_ID_FK) REFERENCES Saved_Detail(Saved_Detail_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Payment (
        Payment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Payment_Amount DECIMAL(10,2) NOT NULL,
        Payment_Date DATE NOT NULL,
        Payment_Method_ID_FK INT(10),
        FOREIGN KEY (Payment_Method_ID_FK) REFERENCES Payment_Method(Payment_Method_ID)
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

    "CREATE TABLE IF NOT EXISTS Reservation_to_Passenger (
        Reservation_to_Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Reservation_ID_FK INT(10),
        Passenger_ID_FK INT(10),
        FOREIGN KEY (Reservation_ID_FK) REFERENCES Reservation(Reservation_ID),
        FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Reservation (
        Reservation_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Booking_date DATE NOT NULL,
        Class VARCHAR(20) NOT NULL,
        Payment_ID_FK INT(10),
        Employee_Assignment_ID_FK INT(10),
        FOREIGN KEY (Payment_ID_FK) REFERENCES Payment(Payment_ID),
        FOREIGN KEY (Employee_Assignment_ID_FK) REFERENCES Employee_Assignment(Employee_Assignment_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Baggage (
        Baggage_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Baggage_Weight VARCHAR(20) NOT NULL,
        Price DECIMAL(10,2) NOT NULL,
        Available_Flights_Number_ID_FK INT(15),
        FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)
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
        FRP_Number_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Available_Flights_Number_ID_FK INT(15),
        Reservation_to_Passenger_ID_FK INT(10),
        FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID),
        FOREIGN KEY (Reservation_to_Passenger_ID_FK) REFERENCES Reservation_to_Passenger(Reservation_to_Passenger_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Add_on (
        Add_on_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        FRP_Number_ID_FK INT(15),
        Seat_Selector_ID_FK INT(10),
        Food_ID_FK INT(10),
        Baggage_ID_FK INT(10),
        FOREIGN KEY (FRP_Number_ID_FK) REFERENCES Flight_to_Reservation_to_Passenger(FRP_Number_ID),
        FOREIGN KEY (Seat_Selector_ID_FK) REFERENCES Seat_Selector(Seat_Selector_ID),
        FOREIGN KEY (Food_ID_FK) REFERENCES Food(Food_ID),
        FOREIGN KEY (Baggage_ID_FK) REFERENCES Baggage(Baggage_ID)
    )",

    "CREATE TABLE IF NOT EXISTS Employee_Assignment (
        Employee_Assignment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
        Employee_Role VARCHAR(50) NOT NULL,
        Employee_ID_FK INT(10),
        Department_ID_FK INT(10),
        Available_Flights_Number_ID_FK INT(15),
        FOREIGN KEY (Employee_ID_FK) REFERENCES Employees(Employee_ID),
        FOREIGN KEY (Department_ID_FK) REFERENCES Department(Department_ID),
        FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)
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
        Seat_Type VARCHAR(20) NOT NULL,
        Passenger_ID_FK INT(10),
        Available_Flights_Number_ID_FK INT(15),
        FOREIGN KEY (Passenger_ID_FK) REFERENCES Passenger(Passenger_ID),
        FOREIGN KEY (Available_Flights_Number_ID_FK) REFERENCES Available_Flights(Available_Flights_Number_ID)
    )"
];

// Execute each table creation query
foreach ($sql_create_tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Close connection
$conn->close();

echo "Database and table setup completed.";
?>
