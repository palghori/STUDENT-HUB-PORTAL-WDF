<?php
// students.php - Student Management CRUD with Search, Filter & Audit Logging
session_start();

// Admin Only Access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fetch info before delete for audit log
    $q_info = mysqli_query($conn, "SELECT full_name, email FROM students WHERE student_id = $id LIMIT 1");
    if ($q_info && mysqli_num_rows($q_info) === 1) {
        $info = mysqli_fetch_assoc($q_info);
        $name_to_del = $info['full_name'];
        $email_to_del = $info['email'];

        $del_query = "DELETE FROM students WHERE student_id = $id";
        if (mysqli_query($conn, $del_query)) {
            record_audit_log($conn, 'DELETE_STUDENT', "Deleted student #$id: $name_to_del ($email_to_del)");
            $success_msg = "Student record deleted successfully!";
        } else {
            $error_msg = "Error deleting student: " . mysqli_error($conn);
        }
    }
}

// Search and Filter logic
$search = trim($_GET['search'] ?? '');
$filter_course = trim($_GET['course'] ?? '');

$query = "SELECT * FROM students WHERE 1=1";

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $query .= " AND (full_name LIKE '%$search_safe%' OR email LIKE '%$search_safe%' OR enrollment_id LIKE '%$search_safe%')";
}

if (!empty($filter_course)) {
    $course_safe = mysqli_real_escape_string($conn, $filter_course);
    $query .= " AND course = '$course_safe'";
}

$query .= " ORDER BY student_id DESC";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Management - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>👥 Student Management</h1>
        <p>Add, search, filter, edit, and delete student records with audit logging</p>
      </div>
      <div>
        <a href="add_student.php" class="btn btn-primary">➕ Add New Student</a>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="card">
      <!-- Search & Course Filter Form -->
      <form action="students.php" method="GET" class="filter-bar">
        <input type="text" name="search" placeholder="Search by name, email, enrollment..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 2; min-width: 200px;">
        
        <select name="course" style="flex: 1; min-width: 150px;">
          <option value="">-- All Courses --</option>
          <option value="Computer Science" <?php echo ($filter_course === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
          <option value="Information Technology" <?php echo ($filter_course === 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
          <option value="Mechanical" <?php echo ($filter_course === 'Mechanical') ? 'selected' : ''; ?>>Mechanical</option>
          <option value="Civil" <?php echo ($filter_course === 'Civil') ? 'selected' : ''; ?>>Civil</option>
          <option value="Administration" <?php echo ($filter_course === 'Administration') ? 'selected' : ''; ?>>Administration</option>
        </select>

        <button type="submit" class="btn btn-primary">🔍 Search</button>
        <?php if (!empty($search) || !empty($filter_course)): ?>
          <a href="students.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Students Table -->
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Enrollment</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Course</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
              <?php while ($s = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><strong>#<?php echo $s['student_id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                  <td><code><?php echo htmlspecialchars($s['enrollment_id']); ?></code></td>
                  <td><?php echo htmlspecialchars($s['email']); ?></td>
                  <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                  <td><?php echo htmlspecialchars($s['course']); ?></td>
                  <td>
                    <span class="badge <?php echo ($s['role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                      <?php echo htmlspecialchars($s['role']); ?>
                    </span>
                  </td>
                  <td>
                    <a href="edit_student.php?id=<?php echo $s['student_id']; ?>" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">✏️ Edit</a>
                    <a href="students.php?delete=<?php echo $s['student_id']; ?>" 
                       class="btn btn-danger" 
                       style="padding: 4px 8px; font-size: 0.8rem;" 
                       onclick="return confirm('Are you sure you want to delete this student record? Action will be logged.');">
                      🗑️ Delete
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" style="text-align: center; color: #888; padding: 25px;">No students match the criteria.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

</body>
</html>
