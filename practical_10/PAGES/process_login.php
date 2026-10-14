<?php
// process_login.php - Handles secure login with sessions

// Start the session at the very top
session_start();

// Include database connection
require_once 'db_connect.php';

// Session Timeout: 15 minutes (900 seconds)
$timeout = 900;

// Check if session already exists and has timed out
if (isset($_SESSION['last_active'])) {
    if ((time() - $_SESSION['last_active']) > $timeout) {
        // Session expired — destroy it and redirect to login
        session_destroy();
        header("Location: login.html?msg=timeout");
        exit();
    }
}

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize inputs
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Don't escape — we use password_verify()

    if (empty($email) || empty($password)) {
        $message = "Error: Please enter both email and password.";
    } else {

        // 2. Find the user by email in the database
        $query = "SELECT * FROM students WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // 3. Verify the entered password against the stored hash
            if (password_verify($password, $user['password_hash'])) {

                // 4. Regenerate session ID after successful login (security best practice)
                session_regenerate_id(true);

                // 5. Store user info in the session
                $_SESSION['student_id']   = $user['student_id'];
                $_SESSION['student_name'] = $user['full_name'];
                $_SESSION['student_email']= $user['email'];
                $_SESSION['role']         = $user['role'];
                $_SESSION['last_active']  = time(); // Record login time for timeout

                // 6. Handle Remember Me
                if (isset($_POST['remember_me'])) {
                    // Store a simple cookie for 7 days
                    setcookie("remember_email", $email, time() + (7 * 24 * 60 * 60), "/");
                }

                // 7. Role-Based Redirection
                if ($user['role'] === 'admin') {
                    // Admin goes to dashboard directly
                    header("Location: dashboard.html?welcome=admin");
                } else {
                    // Student goes to dashboard
                    header("Location: dashboard.html?welcome=student");
                }
                exit();

            } else {
                $message = "Error: Incorrect password. Please try again.";
            }
        } else {
            $message = "Error: No account found with this email.";
        }
    }

    mysqli_close($conn);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Status</title>
    <link rel="stylesheet" href="../CSS/script.css" />
  </head>
  <body>
    <nav class="navbar">
      <a class="brand" href="index.html">StudentHub</a>
      <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
      </div>
    </nav>
    <main>
      <section class="form-box" style="text-align: center;">
        <h2>Login Status</h2>
        <p style="color: #f44336; font-weight: bold; font-size: 1.2rem; margin: 20px 0;">
            <?php echo $message; ?>
        </p>
        <br>
        <a href="login.html" style="color: #f0a060; text-decoration: underline;">Back to Login</a>
      </section>
    </main>
  </body>
</html>
