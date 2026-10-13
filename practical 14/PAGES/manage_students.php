<?php
// manage_students.php - Student Management for Admin with Audit Logging
session_start();

// RBAC: Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// Handle Quick Role Toggle / Action
if (isset($_GET['toggle_role'])) {
    $target_id = (int)$_GET['toggle_role'];
    
    // Fetch student info
    $user_q = mysqli_query($conn, "SELECT full_name, email, role FROM students WHERE student_id = $target_id LIMIT 1");
    if ($user_q && mysqli_num_rows($user_q) === 1) {
        $user_row = mysqli_fetch_assoc($user_q);
        $new_role = ($user_row['role'] === 'admin') ? 'student' : 'admin';
        
        $update_q = "UPDATE students SET role = '$new_role' WHERE student_id = $target_id";
        if (mysqli_query($conn, $update_q)) {
            // Record in audit log
            record_audit_log($conn, 'ROLE_CHANGE', "Changed role of " . $user_row['email'] . " from " . $user_row['role'] . " to " . $new_role);
            $success_msg = "Updated role for " . htmlspecialchars($user_row['full_name']) . " to " . strtoupper($new_role) . " and logged to Audit Log.";
        }
    }
}

// Fetch all students
$result_students = mysqli_query($conn, "SELECT student_id, full_name, enrollment_id, email, mobile, course, role, created_at FROM students ORDER BY student_id ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Students - Practical 14</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>👥 Student Management</h1>
        <p>Admin Control Panel for registered students and role assignments</p>
      </div>
      <div>
        <a href="admin_dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <div class="card">
      <h2>Registered Accounts</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Enrollment</th>
              <th>Email</th>
              <th>Course</th>
              <th>Role</th>
              <th>Actions (Logged)</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($s = mysqli_fetch_assoc($result_students)): ?>
              <tr>
                <td>#<?php echo $s['student_id']; ?></td>
                <td><strong><?php echo htmlspecialchars($s['full_name']); ?></strong></td>
                <td><code><?php echo htmlspecialchars($s['enrollment_id']); ?></code></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo htmlspecialchars($s['course']); ?></td>
                <td>
                  <span class="badge <?php echo ($s['role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                    <?php echo htmlspecialchars($s['role']); ?>
                  </span>
                </td>
                <td>
                  <a href="manage_students.php?toggle_role=<?php echo $s['student_id']; ?>" 
                     class="btn btn-outline" 
                     style="font-size: 0.8rem; padding: 4px 10px;"
                     onclick="return confirm('Toggle role for this user?');">
                    Switch to <?php echo ($s['role'] === 'admin') ? 'Student' : 'Admin'; ?>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 14: Role-Based Access Control</p>
  </footer>

</body>
</html>
