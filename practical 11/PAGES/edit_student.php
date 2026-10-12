<?php
// edit_student.php - SELECT (pre-fill) and UPDATE a student record
require_once 'db_connect.php';

$message = "";
$success = false;

// Get student ID from URL
if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}
$id = (int) $_GET['id'];

// Handle UPDATE when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile  = mysqli_real_escape_string($conn, $_POST['mobile']);
    $course  = mysqli_real_escape_string($conn, $_POST['course']);
    $year    = mysqli_real_escape_string($conn, $_POST['year']);
    $gender  = mysqli_real_escape_string($conn, $_POST['gender']);

    // Basic validation
    if (empty($name) || empty($email)) {
        $message = "Error: Name and Email are required.";
    } else {
        // UPDATE query
        $update = "UPDATE students SET 
                    full_name = '$name',
                    email     = '$email',
                    mobile    = '$mobile',
                    course    = '$course',
                    year      = '$year',
                    gender    = '$gender'
                   WHERE student_id = $id";

        if (mysqli_query($conn, $update)) {
            $success = true;
            $message = "Student updated successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

// SELECT: Fetch current student data to pre-fill the form
$fetch = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $id");
$student = mysqli_fetch_assoc($fetch);

if (!$student) {
    echo "<p>Student not found.</p>";
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Student</title>
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
        <h2>Edit Student</h2>

        <?php if ($message): ?>
            <p style="color:<?php echo $success ? '#4CAF50' : '#f44336'; ?>;font-weight:bold;margin-bottom:12px;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <!-- Form pre-filled with current student data -->
        <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
            <label>Full Name *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required />

            <label>Enrollment ID (cannot be changed)</label>
            <input type="text" value="<?php echo htmlspecialchars($student['enrollment_id']); ?>" disabled />

            <label>Email *</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required />

            <label>Mobile</label>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" />

            <label>Course</label>
            <select name="course">
                <?php
                $courses = ['B.Tech', 'BCA', 'MCA', 'MBA', 'BBA'];
                foreach ($courses as $c) {
                    $sel = ($student['course'] == $c) ? 'selected' : '';
                    echo "<option value='$c' $sel>$c</option>";
                }
                ?>
            </select>

            <label>Year</label>
            <select name="year">
                <?php
                for ($y = 1; $y <= 4; $y++) {
                    $sel = ($student['year'] == $y) ? 'selected' : '';
                    echo "<option value='$y' $sel>{$y}st/nd/rd/th Year</option>";
                }
                ?>
            </select>

            <label>Gender</label>
            <select name="gender">
                <?php
                $genders = ['Male', 'Female', 'Other'];
                foreach ($genders as $g) {
                    $sel = ($student['gender'] == $g) ? 'selected' : '';
                    echo "<option value='$g' $sel>$g</option>";
                }
                ?>
            </select>

            <button type="submit">Update Student</button>
        </form>
        <p style="margin-top:12px;"><a href="students.php" style="color:#f0a060;">← Back to Student List</a></p>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
