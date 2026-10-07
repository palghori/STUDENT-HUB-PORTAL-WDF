<?php
// Include the database connection file
require_once 'db_connect.php';

// Display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
    $enrollment = htmlspecialchars(strip_tags(trim($_POST['enrollment'] ?? '')));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mobile = htmlspecialchars(strip_tags(trim($_POST['mobile'] ?? '')));
    $raw_password = $_POST['password'] ?? '';
    $course = htmlspecialchars(strip_tags(trim($_POST['course'] ?? '')));
    $year = htmlspecialchars(strip_tags(trim($_POST['year'] ?? '')));
    $gender = htmlspecialchars(strip_tags(trim($_POST['gender'] ?? '')));
    
    // Validate
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $message = "Error: Invalid email format.";
    } elseif (empty($name) || empty($enrollment) || empty($email) || empty($mobile) || empty($raw_password)) {
        $message = "Error: Please fill in all required fields.";
    } else {
        // Hash the password for security
        $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);

        try {
            // Prepare an SQL statement using placeholders (?) to prevent SQL Injection
            $sql = "INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            
            // Execute the prepared statement with the sanitized variables
            $stmt->execute([
                $name, 
                $enrollment, 
                $email, 
                $mobile, 
                $password_hash, 
                $course, 
                $year, 
                $gender
            ]);

            $success = true;
            $message = "Registration successful! Account created securely in MySQL.";

        } catch (PDOException $e) {
            // Check if error is due to a duplicate unique key (enrollment or email)
            if ($e->getCode() == 23000) {
                $message = "Error: An account with this Enrollment ID or Email already exists.";
            } else {
                $message = "Database Error: " . $e->getMessage();
            }
        }
    }
} else {
    $message = "Error: Invalid request method.";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Status</title>
    <link rel="stylesheet" href="../CSS/script.css" />
  </head>
  <body>
    <nav class="navbar">
      <a class="brand" href="index.html">StudentHub</a>
      <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="register.html">Register</a>
      </div>
    </nav>
    <main>
      <section class="form-box" style="text-align: center;">
        <h2>Registration Status</h2>
        <p style="color: <?php echo $success ? '#4CAF50' : '#f44336'; ?>; font-weight: bold; font-size: 1.2rem; margin: 20px 0;">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <br>
        <a href="register.html" style="color: #f0a060; text-decoration: underline;">Back to Register</a>
      </section>
    </main>
  </body>
</html>
