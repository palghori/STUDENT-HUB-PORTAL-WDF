<?php
// edit_student.php - Admin form to update student records
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: students.php");
    exit();
}

// Fetch existing student data
$fetch_q = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $id LIMIT 1");
if (!$fetch_q || mysqli_num_rows($fetch_q) === 0) {
    header("Location: students.php");
    exit();
}
$student = mysqli_fetch_assoc($fetch_q);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name     = trim($_POST['full_name'] ?? '');
    $enrollment_id = trim($_POST['enrollment_id'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $mobile        = trim($_POST['mobile'] ?? '');
    $course        = trim($_POST['course'] ?? '');
    $year          = trim($_POST['year'] ?? '');
    $gender        = trim($_POST['gender'] ?? '');
    $role          = trim($_POST['role'] ?? 'student');

    if (empty($full_name) || empty($enrollment_id) || empty($email) || empty($mobile)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        $name_safe   = mysqli_real_escape_string($conn, $full_name);
        $enr_safe    = mysqli_real_escape_string($conn, $enrollment_id);
        $email_safe  = mysqli_real_escape_string($conn, $email);
        $mobile_safe = mysqli_real_escape_string($conn, $mobile);
        $course_safe = mysqli_real_escape_string($conn, $course);
        $year_safe   = mysqli_real_escape_string($conn, $year);
        $gender_safe = mysqli_real_escape_string($conn, $gender);
        $role_safe   = mysqli_real_escape_string($conn, $role);

        $update_sql = "UPDATE students SET 
                       full_name = '$name_safe', 
                       enrollment_id = '$enr_safe', 
                       email = '$email_safe', 
                       mobile = '$mobile_safe', 
                       course = '$course_safe', 
                       year = '$year_safe', 
                       gender = '$gender_safe', 
                       role = '$role_safe' 
                       WHERE student_id = $id";

        if (mysqli_query($conn, $update_sql)) {
            record_audit_log($conn, 'UPDATE_STUDENT', "Admin updated student #$id: $name_safe ($email_safe)");
            header("Location: students.php");
            exit();
        } else {
            $error_msg = "Update error: " . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Student - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box" style="max-width: 580px;">
      <h2>✏️ Edit Student Record #<?php echo $id; ?></h2>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="edit_student.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
          <label>Full Name:</label>
          <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($student['full_name']); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Enrollment ID:</label>
            <input type="text" name="enrollment_id" class="form-control" required value="<?php echo htmlspecialchars($student['enrollment_id']); ?>">
          </div>
          <div class="form-group">
            <label>Mobile:</label>
            <input type="tel" name="mobile" class="form-control" required value="<?php echo htmlspecialchars($student['mobile']); ?>">
          </div>
        </div>

        <div class="form-group">
          <label>Email Address:</label>
          <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($student['email']); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Course:</label>
            <select name="course" class="form-control">
              <option value="Computer Science" <?php echo ($student['course'] === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
              <option value="Information Technology" <?php echo ($student['course'] === 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
              <option value="Mechanical" <?php echo ($student['course'] === 'Mechanical') ? 'selected' : ''; ?>>Mechanical</option>
              <option value="Civil" <?php echo ($student['course'] === 'Civil') ? 'selected' : ''; ?>>Civil</option>
              <option value="Administration" <?php echo ($student['course'] === 'Administration') ? 'selected' : ''; ?>>Administration</option>
            </select>
          </div>
          <div class="form-group">
            <label>Year:</label>
            <select name="year" class="form-control">
              <option value="1st Year" <?php echo ($student['year'] === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
              <option value="2nd Year" <?php echo ($student['year'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
              <option value="3rd Year" <?php echo ($student['year'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
              <option value="Final Year" <?php echo ($student['year'] === 'Final Year') ? 'selected' : ''; ?>>Final Year</option>
            </select>
          </div>
          <div class="form-group">
            <label>Gender:</label>
            <select name="gender" class="form-control">
              <option value="Male" <?php echo ($student['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
              <option value="Female" <?php echo ($student['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
              <option value="Other" <?php echo ($student['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Role:</label>
          <select name="role" class="form-control">
            <option value="student" <?php echo ($student['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="admin" <?php echo ($student['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
          </select>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Update Student</button>
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
