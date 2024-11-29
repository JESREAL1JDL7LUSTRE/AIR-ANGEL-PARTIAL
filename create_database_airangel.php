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
    SD_Account_ID_FK int(10) FOREIGN KEY REFERENCESL Saved_Detail_in_Account(SD_Account_ID),
    Account_to_Passenger_ID_FK int(10) FOREIGN KEY REFERENCES Account_to_Passenger(Account_to_Passenger_ID),
    Reservation_to_Account_ID_FK int(10) FOREIGN KEY REFERENCES Reservation_to_account(Reservation_to_Account_ID),
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
$sql_create_table_Saved_Detail_in_Account = "
CREATE TABLE IF NOT EXISTS Saved_Detail_in_Account (
    SD_Account_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Account_ID_FK int(10) FOREIGN KEY REFERENCES Account to Account(Account_ID),
    Saved_Detail_ID_FK int(10) FOREIGN KEY REFERENCES Reservation to Saved_Detail(Saved_Detail_ID),
)";
$sql_create_table_Payment = "
CREATE TABLE IF NOT EXISTS Payment (
    Payment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Payment_Amount DECIMAL(10,2) NOT NULL,
    Payment_Date DATE NOT NULL,
    Payment_Method_ID_FK int(10) FOREIGN KEY REFERENCESL Payment_Method(Payment_Method_ID),
)";
$sql_create_table_Passenger = "
CREATE TABLE IF NOT EXISTS Passenger (
    Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Passenger_Last_Name VARCHAR(50) NOT NULL,
    Passenger_First_Name VARCHAR(50) NOT NULL,
    Passenger_Middle_Name VARCHAR(50) NOT NULL,
    Passenger_Birthday DATE NOT NULL,
    Passenger_Nationality VARCHAR(50) NOT NULL,
    Passenger_Email VARCHAR(50) NOT NULL,
    Passenger_PhoneNumber VARCHAR(11) NOT NULL,
    Passenger_Emgergency_Contact_No VARCHAR(11) NOT NULL,
)";
$sql_create_table_Reservation_to_Passenger = "
CREATE TABLE IF NOT EXISTS Reservation_to_Passenger (
    Reservation_to_Passenger_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Reservation_ID_FK int(10) FOREIGN KEY REFERENCESL Reservation(Reservation_ID),
    Passenger_ID_FK int(10) FOREIGN KEY REFERENCES Passenger(Passenger_ID),
)";
$sql_create_table_Reservation = "
CREATE TABLE IF NOT EXISTS Reservation (
    Reservation_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Booking_date DATE NOT NULL,
    Class VARCHAR(20) NOT NULL,
    Payment_ID_FK int(10) FOREIGN KEY REFERENCES Account to Payment(Payment_ID),
    Employee_Assignment_ID_FK int(10) FOREIGN KEY REFERENCES Employee_Assignment(Employee_Assignment_ID),
)";
$sql_create_table_Baggage = "
CREATE TABLE IF NOT EXISTS Baggage (
    Baggage_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Baggage_Weight VARCHAR(20) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Available_Flights_Number_ID_FK VARCHAR(15) FOREIGN KEY REFERENCES Available_Flights(Available_Flights_Number_ID),
)";
$sql_create_table_Available_Flights = "
CREATE TABLE IF NOT EXISTS Available_Flights (
    Available_Flights_Number_ID VARCHAR(15) AUTO_INCREMENT PRIMARY KEY,
    Departure_Date DATE NOT NULL,
    Arrival_Date DATE NOT NULL,
    Origin VARCHAR(50) NOT NULL,
    Destination VARCHAR(50) NOT NULL,
    Departure_Time TIME NOT NULL,
    Arrival_Time TIME) NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
)";
$sql_create_table_Flight_to_Reservation_to_Passenger = "
CREATE TABLE IF NOT EXISTS Flight_to_Reservation_to_Passenger (
    FRP_Number_ID VARCHAR(10) AUTO_INCREMENT PRIMARY KEY,
    Available_Flights_Number_ID_FK VARCHAR(15) FOREIGN KEY REFERENCES Available_Flights(Available_Flights_Number_ID),
    Reservation_to_Passenger_ID_FK int(10) FOREIGN KEY REFERENCES Reservation_to_Passenger(Reservation_to_Passenger_ID),
)";
$sql_create_table_Add_on= "
CREATE TABLE IF NOT EXISTS Add_on (
    Add_on_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    FRP_Number_ID_FK VARCHAR(15) FOREIGN KEY REFERENCESL Flight_to_Reservation_to_Passenger(FRP_Number_ID),
    Seat_Selector_ID_FK int(10) FOREIGN KEY REFERENCES Seat_Selector(Seat_Selector_ID),
    Food_ID_FK int(10) FOREIGN KEY REFERENCES Food(Food_ID),
    Baggage_ID_FK int(10) FOREIGN KEY REFERENCES Baggage(Baggage_ID),
)";
$sql_create_table_Employee_Assignment = "
CREATE TABLE IF NOT EXISTS Employee_Assignment (
    Employee_Assignment_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Employee_Role VARCHAR(50) NOT NULL,
    Employee_ID_FK int(10) FOREIGN KEY REFERENCESL Employees(Employee_ID),
    Department_ID_FK int(10) FOREIGN KEY REFERENCES Department(Department_ID),
    Available_Flights_Number_ID_FK VARCHAR(15) FOREIGN KEY REFERENCES Available_Flights(Available_Flights_Number_ID),
)";
$sql_create_table_Department = "
CREATE TABLE IF NOT EXISTS Department (
    Department_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Department_Name VARCHAR(30) NOT NULL,
    Department_Type VARCHAR(20) NOT NULL,
)";
$sql_create_table_Employees = "
CREATE TABLE IF NOT EXISTS Employees (
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
    Employee_Health_Insurance VARCHAR(50) NOT NULL,
)";
$sql_create_table_Seat_Selector = "
CREATE TABLE IF NOT EXISTS Seat_Selector (
    Seat_Selector_ID INT(10) AUTO_INCREMENT PRIMARY KEY,
    Seat_Selector_Number VARCHAR(10) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Seat_Type VARCHAR(20) NOT NULL,
    Passenger_ID_FK int(10) FOREIGN KEY REFERENCES Passenger(Passenger_ID),
    Available_Flights_Number_ID_FK VARCHAR(15) FOREIGN KEY REFERENCES Available_Flights(Available_Flights_Number_ID),
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
