<?php
// Include the basic database connection
require_once 'db_connect.php';

$message = "";
$success = false;

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Basic Sanitization (preventing basic SQL Injection)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $enrollment = mysqli_real_escape_string($conn, $_POST['enrollment']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $raw_password = $_POST['password']; // Don't escape yet, we will hash it
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    
    // 2. Basic Backend Validation
    if (empty($name) || empty($enrollment) || empty($email) || empty($raw_password)) {
        $message = "Error: Please fill in all required fields.";
    } else {
        // 3. Check for Duplicate Email or Enrollment ID
        // Simple SELECT query to check if record exists
        $check_query = "SELECT * FROM students WHERE email = '$email' OR enrollment_id = '$enrollment'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            // Duplicate found!
            $message = "Error: An account with this Email or Enrollment ID already exists!";
        } else {
            // 4. Secure Password Hashing
            // Using PHP's built-in password_hash (a basic but secure method)
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

            // 5. Database Insert Query
            $insert_query = "INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender) 
                             VALUES ('$name', '$enrollment', '$email', '$mobile', '$hashed_password', '$course', '$year', '$gender')";
            
            // Execute the insert query
            if (mysqli_query($conn, $insert_query)) {
                $success = true;
                $message = "Registration successful! Account securely created.";
            } else {
                $message = "Error inserting data: " . mysqli_error($conn);
            }
        }
    }
}

// Close the database connection
mysqli_close($conn);
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
        
        <!-- Display the success or error message -->
        <p style="color: <?php echo $success ? '#4CAF50' : '#f44336'; ?>; font-weight: bold; font-size: 1.2rem; margin: 20px 0;">
            <?php echo $message; ?>
        </p>
        
        <br>
        <a href="register.html" style="color: #f0a060; text-decoration: underline;">Back to Register</a>
      </section>
    </main>
  </body>
</html>
