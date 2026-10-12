<?php
// add_student.php - INSERT a new student record
require_once 'db_connect.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $enrollment = mysqli_real_escape_string($conn, $_POST['enrollment']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile     = mysqli_real_escape_string($conn, $_POST['mobile']);
    $course     = mysqli_real_escape_string($conn, $_POST['course']);
    $year       = mysqli_real_escape_string($conn, $_POST['year']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);
    $password   = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($enrollment) || empty($email) || empty($password)) {
        $message = "Error: Please fill in all required fields.";
    } else {
        // Check for duplicate
        $check = mysqli_query($conn, "SELECT student_id FROM students WHERE email='$email' OR enrollment_id='$enrollment'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Error: A student with this Email or Enrollment ID already exists!";
        } else {
            // Hash password and INSERT
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender)
                       VALUES ('$name', '$enrollment', '$email', '$mobile', '$hashed', '$course', '$year', '$gender')";
            if (mysqli_query($conn, $insert)) {
                $success = true;
                $message = "Student added successfully!";
            } else {
                $message = "Error: " . mysqli_error($conn);
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
    <title>Add Student</title>
    <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>
<nav class="navbar">
    <a class="brand" href="#">StudentHub</a>
    <div class="nav-links">
        <a href="students.php">Manage Students</a>
        <a href="add_student.php">Add Student</a>
    </div>
</nav>
<main>
    <section class="form-box">
        <h2>Add New Student</h2>

        <?php if ($message): ?>
            <p style="color:<?php echo $success ? '#4CAF50' : '#f44336'; ?>;font-weight:bold;margin-bottom:12px;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="add_student.php">
            <label>Full Name *</label>
            <input type="text" name="name" placeholder="Enter full name" required />

            <label>Enrollment ID *</label>
            <input type="text" name="enrollment" placeholder="e.g. 25DCE030" required />

            <label>Email *</label>
            <input type="email" name="email" placeholder="student@example.com" required />

            <label>Mobile</label>
            <input type="text" name="mobile" placeholder="10-digit mobile number" />

            <label>Course</label>
            <select name="course">
                <option value="B.Tech">B.Tech</option>
                <option value="BCA">BCA</option>
                <option value="MCA">MCA</option>
                <option value="MBA">MBA</option>
                <option value="BBA">BBA</option>
            </select>

            <label>Year</label>
            <select name="year">
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>

            <label>Gender</label>
            <select name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label>Password *</label>
            <input type="password" name="password" placeholder="Set a password" required />

            <button type="submit">Add Student</button>
        </form>
        <p style="margin-top:12px;"><a href="students.php" style="color:#f0a060;">← Back to Student List</a></p>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
