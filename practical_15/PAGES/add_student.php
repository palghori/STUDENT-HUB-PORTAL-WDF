<?php
// add_student.php - Admin form to add a new student
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name     = trim($_POST['full_name'] ?? '');
    $enrollment_id = trim($_POST['enrollment_id'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');
    $password      = $_POST['password'] ?? 'student123';
    $course        = trim($_POST['course'] ?? '');
    $year          = trim($_POST['year'] ?? '');
    $gender        = trim($_POST['gender'] ?? '');
    $role          = trim($_POST['role'] ?? 'student');

    if (empty($full_name) || empty($enrollment_id) || empty($email) || empty($mobile) || empty($course)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        $email_safe      = mysqli_real_escape_string($conn, $email);
        $enrollment_safe = mysqli_real_escape_string($conn, $enrollment_id);

        $check = mysqli_query($conn, "SELECT * FROM students WHERE email = '$email_safe' OR enrollment_id = '$enrollment_safe'");
        if ($check && mysqli_num_rows($check) > 0) {
            $error_msg = "Duplicate error: Email or Enrollment ID is already registered.";
        } else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $name_safe   = mysqli_real_escape_string($conn, $full_name);
            $mobile_safe = mysqli_real_escape_string($conn, $mobile);
            $course_safe = mysqli_real_escape_string($conn, $course);
            $year_safe   = mysqli_real_escape_string($conn, $year);
            $gender_safe = mysqli_real_escape_string($conn, $gender);
            $role_safe   = mysqli_real_escape_string($conn, $role);

            $sql = "INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
                    VALUES ('$name_safe', '$enrollment_safe', '$email_safe', '$mobile_safe', '$pass_hash', '$course_safe', '$year_safe', '$gender_safe', '$role_safe')";

            if (mysqli_query($conn, $sql)) {
                record_audit_log($conn, 'ADD_STUDENT', "Admin added student: $name_safe ($email_safe)");
                header("Location: students.php");
                exit();
            } else {
                $error_msg = "Error inserting record: " . mysqli_error($conn);
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
  <title>Add Student - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box" style="max-width: 580px;">
      <h2>➕ Add New Student Record</h2>
      
      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="add_student.php" method="POST">
        <div class="form-group">
          <label>Full Name:</label>
          <input type="text" name="full_name" class="form-control" required placeholder="Full Name">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Enrollment ID:</label>
            <input type="text" name="enrollment_id" class="form-control" required placeholder="e.g. ENR202610">
          </div>
          <div class="form-group">
            <label>Mobile:</label>
            <input type="tel" name="mobile" class="form-control" required placeholder="10-digit mobile">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" class="form-control" required placeholder="student@example.com">
          </div>
          <div class="form-group">
            <label>Initial Password:</label>
            <input type="password" name="password" class="form-control" required value="student123">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Course:</label>
            <select name="course" class="form-control">
              <option value="Computer Science">Computer Science</option>
              <option value="Information Technology">Information Technology</option>
              <option value="Mechanical">Mechanical</option>
              <option value="Civil">Civil</option>
            </select>
          </div>
          <div class="form-group">
            <label>Year:</label>
            <select name="year" class="form-control">
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="Final Year">Final Year</option>
            </select>
          </div>
          <div class="form-group">
            <label>Gender:</label>
            <select name="gender" class="form-control">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Assign Role:</label>
          <select name="role" class="form-control">
            <option value="student">Student</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Save Student Record</button>
          <a href="students.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

</body>
</html>
