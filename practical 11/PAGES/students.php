<?php
// students.php - View, Search, Filter and Delete Students
require_once 'db_connect.php';

$success_msg = "";
$error_msg = "";

// --- DELETE Operation ---
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete']; // Cast to integer for safety
    $del_query = "DELETE FROM students WHERE student_id = $id";
    if (mysqli_query($conn, $del_query)) {
        $success_msg = "Student deleted successfully!";
    } else {
        $error_msg = "Error deleting student: " . mysqli_error($conn);
    }
}

// --- SEARCH & FILTER ---
$search = "";
$filter_course = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}
if (isset($_GET['course'])) {
    $filter_course = mysqli_real_escape_string($conn, $_GET['course']);
}

// Build the SELECT query with optional search and filter
$query = "SELECT * FROM students WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($filter_course)) {
    $query .= " AND course = '$filter_course'";
}

$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Management</title>
    <link rel="stylesheet" href="../CSS/script.css" />
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px 14px; border: 1px solid #444; text-align: left; font-size: 0.9rem; }
        th { background: #f0a060; color: #111; }
        tr:nth-child(even) { background: rgba(255,255,255,0.05); }
        .action-btns a { margin-right: 8px; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; }
        .btn-edit { background: #4a90e2; color: white; }
        .btn-delete { background: #e25555; color: white; }
        .search-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .search-bar input, .search-bar select { padding: 8px 12px; border-radius: 6px; border: 1px solid #555; background: #222; color: #eee; flex: 1; }
        .search-bar button { padding: 8px 18px; border-radius: 6px; border: none; background: #f0a060; color: #111; font-weight: bold; cursor: pointer; }
        .msg-success { color: #4CAF50; font-weight: bold; margin-bottom: 12px; }
        .msg-error { color: #f44336; font-weight: bold; margin-bottom: 12px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    </style>
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
    <section class="form-box" style="max-width: 1100px;">
        <div class="top-bar">
            <h2>Student Management</h2>
            <a href="add_student.php" style="background:#4CAF50;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:bold;">+ Add New Student</a>
        </div>

        <!-- Messages -->
        <?php if ($success_msg): ?>
            <p class="msg-success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <p class="msg-error"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <form method="GET" action="students.php">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search by name or email..."
                       value="<?php echo htmlspecialchars($search); ?>" />
                <select name="course">
                    <option value="">-- All Courses --</option>
                    <option value="B.Tech" <?php echo $filter_course == 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                    <option value="BCA"    <?php echo $filter_course == 'BCA'    ? 'selected' : ''; ?>>BCA</option>
                    <option value="MCA"    <?php echo $filter_course == 'MCA'    ? 'selected' : ''; ?>>MCA</option>
                    <option value="MBA"    <?php echo $filter_course == 'MBA'    ? 'selected' : ''; ?>>MBA</option>
                    <option value="BBA"    <?php echo $filter_course == 'BBA'    ? 'selected' : ''; ?>>BBA</option>
                </select>
                <button type="submit">Search</button>
                <a href="students.php" style="padding:8px 14px;border-radius:6px;background:#555;color:white;text-decoration:none;">Reset</a>
            </div>
        </form>

        <!-- Students Table -->
        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Enrollment ID</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['enrollment_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td class="action-btns">
                        <a class="btn-edit" href="edit_student.php?id=<?php echo $row['student_id']; ?>">Edit</a>
                        <a class="btn-delete" href="students.php?delete=<?php echo $row['student_id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="margin-top:20px; color:#aaa;">No students found. <a href="add_student.php" style="color:#f0a060;">Add one now.</a></p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
