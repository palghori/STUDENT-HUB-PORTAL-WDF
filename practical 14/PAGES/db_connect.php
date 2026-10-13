<?php
// db_connect.php - Basic MySQLi Connection for Practical 14

$host = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password is empty
$dbname = "studenthub_db";

// 1. Create a basic connection using mysqli_connect()
$conn = mysqli_connect($host, $username, $password, $dbname);

// 2. Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
