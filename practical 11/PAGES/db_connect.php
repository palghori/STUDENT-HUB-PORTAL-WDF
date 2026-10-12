<?php
// db_connect.php - Basic MySQLi Connection

$host = "localhost";
$username = "root"; // Default XAMPP user
$password = "";     // Default XAMPP password is empty
$dbname = "studenthub_db";

// 1. Create a basic connection using mysqli_connect()
$conn = mysqli_connect($host, $username, $password, $dbname);

// 2. Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
