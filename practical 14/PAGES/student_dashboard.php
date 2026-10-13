<?php
// student_dashboard.php - Student Dashboard with Dynamic Menu & RBAC Demonstration
session_start();

// Ensure user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$student_name  = $_SESSION['full_name'] ?? 'Student User';
$student_email = $_SESSION['email'] ?? '';
$student_role  = $_SESSION['role'] ?? 'student';

// Fetch available events for student
$sql_events = "SELECT * FROM events ORDER BY event_date ASC";
$result_events = mysqli_query($conn, $sql_events);

// Fetch total events
$total_events = mysqli_num_rows($result_events);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard - Practical 14</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <!-- Dynamic Role-Based Menu (renders student links) -->
  <?php include 'navbar.php'; ?>

  <main>
    <!-- Header Banner -->
    <div class="dashboard-header" style="border-left-color: #1e88e5;">
      <div>
        <h1>🎓 Student Portal</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars($student_name); ?></strong></p>
      </div>
      <div class="header-user-info">
        <span class="badge badge-student">Role: <?php echo htmlspecialchars(strtoupper($student_role)); ?></span>
        <span style="color: #666; font-size: 0.9rem;"><code><?php echo htmlspecialchars($student_email); ?></code></span>
      </div>
    </div>

    <!-- 1. RBAC DEMO BOX -->
    <div class="card" style="border: 2px dashed #e74c3c; background: #fffdfd;">
      <h2 style="color: #c0392b;">🛡️ Role-Based Access Control (RBAC) Demonstration</h2>
      <p style="color: #555; font-size: 0.95rem;">
        You are currently authenticated as a standard <strong>Student</strong> user.
        According to the system's security policy, non-admin users are strictly barred from administrative routes.
      </p>

      <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 15px;">
        <!-- Clicking this tests RBAC enforcement! -->
        <a href="admin_dashboard.php" class="btn btn-danger">
          ⚠️ Try Accessing Admin Dashboard (admin_dashboard.php)
        </a>
        <a href="audit_logs.php" class="btn btn-outline" style="border-color: #c0392b; color: #c0392b;">
          ⚠️ Try Accessing Audit Logs (audit_logs.php)
        </a>
        <a href="login.php?demo=admin" class="btn btn-success">
          🔄 Switch to Admin Account
        </a>
      </div>
      <small style="display: block; margin-top: 10px; color: #888;">
        Notice that clicking the red test buttons will trigger an immediate <strong>403 Access Denied</strong> redirect because your session role is <code><?php echo htmlspecialchars($student_role); ?></code>.
      </small>
    </div>

    <!-- 2. UPCOMING EVENTS -->
    <div class="card">
      <h2>📅 Upcoming Campus Events</h2>
      <p style="color: #666; margin-top: -8px;">Explore university events and workshops:</p>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Event Title</th>
              <th>Category</th>
              <th>Event Date</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_events && mysqli_num_rows($result_events) > 0): ?>
              <?php while ($evt = mysqli_fetch_assoc($result_events)): ?>
                <tr>
                  <td>#<?php echo $evt['event_id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($evt['title']); ?></strong></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($evt['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($evt['event_date']); ?></td>
                  <td><?php echo htmlspecialchars($evt['description']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; color: #888; padding: 20px;">No events currently listed.</td>
              </tr>
            <?php endif; ?>
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
