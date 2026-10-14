<?php
// register.php - Student Registration with Password Hashing & Duplicate Validation
session_start();
require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name     = trim($_POST['full_name'] ?? '');
    $enrollment_id = trim($_POST['enrollment_id'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');
    $password      = $_POST['password'] ?? '';
    $course        = trim($_POST['course'] ?? '');
    $year          = trim($_POST['year'] ?? '');
    $gender        = trim($_POST['gender'] ?? '');

    if (empty($full_name) || empty($enrollment_id) || empty($email) || empty($mobile) || empty($password) || empty($course) || empty($year) || empty($gender)) {
        $error_msg = "All fields are required. Please complete the form.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {
        $email_safe      = mysqli_real_escape_string($conn, $email);
        $enrollment_safe = mysqli_real_escape_string($conn, $enrollment_id);

        // Check for duplicate email or enrollment ID
        $check_q = "SELECT * FROM students WHERE email = '$email_safe' OR enrollment_id = '$enrollment_safe'";
        $check_res = mysqli_query($conn, $check_q);

        if ($check_res && mysqli_num_rows($check_res) > 0) {
            $error_msg = "An account with this email address or enrollment ID already exists.";
        } else {
            // Hash password securely
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $name_safe   = mysqli_real_escape_string($conn, $full_name);
            $mobile_safe = mysqli_real_escape_string($conn, $mobile);
            $course_safe = mysqli_real_escape_string($conn, $course);
            $year_safe   = mysqli_real_escape_string($conn, $year);
            $gender_safe = mysqli_real_escape_string($conn, $gender);

            $insert_sql = "INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role) 
                           VALUES ('$name_safe', '$enrollment_safe', '$email_safe', '$mobile_safe', '$password_hash', '$course_safe', '$year_safe', '$gender_safe', 'student')";

            if (mysqli_query($conn, $insert_sql)) {
                record_audit_log($conn, 'NEW_USER_REGISTRATION', "New student registered: $name_safe ($email_safe)");
                header("Location: login.php?msg=registered");
                exit();
            } else {
                $error_msg = "Database error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Registration - StudentHub Portal</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box" style="max-width: 580px;">
      <h2 style="text-align: center; color: #8b5e3c; margin-top: 0;">📝 Create Student Account</h2>
      <p style="text-align: center; color: #7d6550; margin-bottom: 24px; font-size: 0.95rem;">
        Fill in your academic details to register
      </p>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="full_name">Full Name:</label>
          <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="e.g. Rahul Sharma" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label for="enrollment_id">Enrollment ID:</label>
            <input type="text" id="enrollment_id" name="enrollment_id" class="form-control" required placeholder="e.g. ENR202601" />
          </div>
          <div class="form-group">
            <label for="mobile">Mobile Number:</label>
            <input type="tel" id="mobile" name="mobile" class="form-control" required placeholder="e.g. 9876543210" />
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email Address:</label>
          <input type="email" id="email" name="email" class="form-control" required placeholder="e.g. rahul@example.com" />
        </div>

        <div class="form-group">
          <label for="password">Password (min 6 characters):</label>
          <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="course" class="form-control" required>
              <option value="Computer Science">B.Tech CS</option>
              <option value="Information Technology">B.Tech IT</option>
              <option value="Mechanical">B.Tech ME</option>
              <option value="Civil">B.Tech CE</option>
            </select>
          </div>
          <div class="form-group">
            <label for="year">Year:</label>
            <select id="year" name="year" class="form-control" required>
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="Final Year">Final Year</option>
            </select>
          </div>
          <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" class="form-control" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
          Complete Registration
        </button>
      </form>

      <p style="text-align: center; margin-top: 16px; font-size: 0.92rem;">
        Already registered? <a href="login.php">Sign in here</a>
      </p>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

</body>
</html>
