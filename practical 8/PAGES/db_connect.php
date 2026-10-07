<?php
// db_connect.php - Handles connection to the MySQL database using PDO

$host = "localhost";
$dbname = "studenthub_db";
$username = "root"; // Default XAMPP user
$password = "";     // Default XAMPP password is empty

try {
    // 1. Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // 2. Set PDO to throw exceptions on errors (Intermediate best practice)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Stop execution and show error if connection fails
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>
