<?php
// db_connect.php - Standard MySQLi Database Connection for Practical 15

$host = "localhost";
$username = "root"; // Default XAMPP user
$password = "";     // Default XAMPP password
$dbname = "studenthub_db";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
