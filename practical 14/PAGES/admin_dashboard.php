<?php
// admin_dashboard.php - Admin Dashboard with RBAC, Dynamic Statistics, Latest Registrations & Audit Logs
session_start();

// -------------------------------------------------------------
// 1. ROLE-BASED ACCESS CONTROL (RBAC) CHECK
// Only users with role === 'admin' can view this dashboard!
// -------------------------------------------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not admin, block access and redirect to access_denied.php
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// -------------------------------------------------------------
// 2. HANDLE ADMIN ACTIONS (Records into audit_logs table)
// -------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['admin_action'])) {
    $action_type = $_POST['admin_action'];

    if ($action_type === 'ANNOUNCEMENT') {
        $announcement_text = trim($_POST['announcement_text'] ?? '');
        if (!empty($announcement_text)) {
            record_audit_log($conn, 'BROADCAST_ANNOUNCEMENT', "Admin posted announcement: " . $announcement_text);
            $success_msg = "Announcement broadcasted and recorded in Audit Log!";
        } else {
            $error_msg = "Announcement text cannot be empty.";
        }
    } elseif ($action_type === 'SYSTEM_HEALTH_CHECK') {
        record_audit_log($conn, 'SYSTEM_CHECK', "Admin performed database and server health diagnostics");
        $success_msg = "System health check executed successfully and logged in Audit Log!";
    } elseif ($action_type === 'TEST_BACKUP') {
        record_audit_log($conn, 'BACKUP_SIMULATION', "Admin initiated database backup snapshot");
        $success_msg = "Backup operation triggered and logged in Audit Log!";
    }
}

// -------------------------------------------------------------
// 3. LOAD DYNAMIC DASHBOARD VALUES FROM DATABASE
// -------------------------------------------------------------

// Total Students Count (role = 'student')
$q_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE role = 'student'");
$row_students = mysqli_fetch_assoc($q_students);
$student_count = $row_students['total'] ?? 0;

// Total Admins Count (role = 'admin')
$q_admins = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE role = 'admin'");
$row_admins = mysqli_fetch_assoc($q_admins);
$admin_count = $row_admins['total'] ?? 0;

// Total Events Count
$q_events = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
$row_events = mysqli_fetch_assoc($q_events);
$event_count = $row_events['total'] ?? 0;

// Total Registrations Count
$q_regs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
$row_regs = mysqli_fetch_assoc($q_regs);
$registration_count = $row_regs['total'] ?? 0;

// Total Audit Logs Count
$q_logs_count = mysqli_query($conn, "SELECT COUNT(*) AS total FROM audit_logs");
$row_logs_count = mysqli_fetch_assoc($q_logs_count);
$audit_log_count = $row_logs_count['total'] ?? 0;

// -------------------------------------------------------------
// 4. LOAD LATEST REGISTRATIONS (JOIN students + events)
// -------------------------------------------------------------
$sql_latest_regs = "
    SELECT r.registration_id, s.full_name AS student_name, s.email, e.title AS event_title, r.registration_date 
    FROM registrations r
    JOIN students s ON r.student_id = s.student_id
    JOIN events e ON r.event_id = e.event_id
    ORDER BY r.registration_date DESC, r.registration_id DESC
    LIMIT 5
";
$result_latest_regs = mysqli_query($conn, $sql_latest_regs);

// -------------------------------------------------------------
// 5. LOAD RECENT AUDIT LOGS (ORDER BY log_time DESC)
// -------------------------------------------------------------
$sql_recent_logs = "
    SELECT * FROM audit_logs 
    ORDER BY log_time DESC, log_id DESC 
    LIMIT 6
";
$result_recent_logs = mysqli_query($conn, $sql_recent_logs);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Practical 14</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <!-- Dynamic Role-Based Navigation Bar -->
  <?php include 'navbar.php'; ?>

  <main>
    <!-- Header Banner -->
    <div class="dashboard-header">
      <div>
        <h1>📊 Administrator Dashboard</h1>
        <p>Real-Time Portal Metrics, Role-Based Access Control & Audit Trail</p>
      </div>
      <div class="header-user-info">
        <span class="badge badge-admin">Role: ADMIN</span>
        <span style="color: #6d4423; font-weight: 600;">
          👤 <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>
        </span>
      </div>
    </div>

    <!-- Feedback Alerts -->
    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <!-- 1. DYNAMIC STATISTICS CARDS -->
    <section class="stats-grid">
      <!-- Total Students -->
      <div class="stat-card">
        <div class="stat-icon students">👥</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$student_count; ?></span>
          <span class="stat-label">Active Students</span>
        </div>
      </div>

      <!-- Total Events -->
      <div class="stat-card">
        <div class="stat-icon events">📅</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$event_count; ?></span>
          <span class="stat-label">Total Events</span>
        </div>
      </div>

      <!-- Total Registrations -->
      <div class="stat-card">
        <div class="stat-icon regs">📝</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$registration_count; ?></span>
          <span class="stat-label">Registrations</span>
        </div>
      </div>

      <!-- Admin Users -->
      <div class="stat-card">
        <div class="stat-icon admins">🛡️</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$admin_count; ?></span>
          <span class="stat-label">Admin Accounts</span>
        </div>
      </div>

      <!-- Audit Logs Count -->
      <div class="stat-card">
        <div class="stat-icon logs">📜</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$audit_log_count; ?></span>
          <span class="stat-label">Audit Log Entries</span>
        </div>
      </div>
    </section>

    <!-- 2. QUICK ADMIN ACTION BOX (Generates Audit Logs) -->
    <div class="card">
      <h2>⚡ Administrative Actions (Audit Log Generator)</h2>
      <p style="color: #666; font-size: 0.95rem; margin-top: -8px;">
        Executing any of these administrative actions will automatically create a timestamped record in the <code>audit_logs</code> table.
      </p>

      <form action="admin_dashboard.php" method="POST" style="margin-top: 15px;">
        <input type="hidden" name="admin_action" value="ANNOUNCEMENT">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <input type="text" name="announcement_text" class="form-control" style="flex: 1; min-width: 250px;" placeholder="Type an announcement to broadcast..." required>
          <button type="submit" class="btn btn-primary">📢 Broadcast & Log</button>
        </div>
      </form>

      <div class="quick-actions">
        <form action="admin_dashboard.php" method="POST" style="display: inline;">
          <input type="hidden" name="admin_action" value="SYSTEM_HEALTH_CHECK">
          <button type="submit" class="btn btn-outline" style="border-color: #27ae60; color: #27ae60;">
            🩺 Run System Diagnostics & Log
          </button>
        </form>

        <form action="admin_dashboard.php" method="POST" style="display: inline;">
          <input type="hidden" name="admin_action" value="TEST_BACKUP">
          <button type="submit" class="btn btn-outline" style="border-color: #8e44ad; color: #8e44ad;">
            💾 Trigger Snapshot Backup & Log
          </button>
        </form>

        <a href="manage_students.php" class="btn btn-outline">
          👥 Manage Students
        </a>

        <a href="manage_events.php" class="btn btn-outline">
          📅 Manage Events
        </a>

        <a href="audit_logs.php" class="btn btn-outline" style="border-color: #d35400; color: #d35400;">
          📜 View All Audit Logs
        </a>
      </div>
    </div>

    <!-- 3. LATEST REGISTRATIONS TABLE -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">📋 Latest Registrations</h2>
        <span style="font-size: 0.85rem; color: #888;">Loaded dynamically via SQL JOIN</span>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Reg ID</th>
              <th>Student Name</th>
              <th>Student Email</th>
              <th>Event Title</th>
              <th>Registration Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_latest_regs && mysqli_num_rows($result_latest_regs) > 0): ?>
              <?php while ($reg = mysqli_fetch_assoc($result_latest_regs)): ?>
                <tr>
                  <td><strong>#<?php echo $reg['registration_id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($reg['student_name']); ?></td>
                  <td><code><?php echo htmlspecialchars($reg['email']); ?></code></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($reg['event_title']); ?></span></td>
                  <td><?php echo htmlspecialchars($reg['registration_date']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; color: #888; padding: 20px;">No registrations recorded yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 4. RECENT AUDIT LOGS TABLE -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">📜 Recent Audit Log Trail</h2>
        <a href="audit_logs.php" class="btn btn-outline" style="font-size: 0.85rem; padding: 6px 12px;">View All Logs &rarr;</a>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Log ID</th>
              <th>Action</th>
              <th>User Email</th>
              <th>Role</th>
              <th>Details</th>
              <th>IP Address</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_recent_logs && mysqli_num_rows($result_recent_logs) > 0): ?>
              <?php while ($log = mysqli_fetch_assoc($result_recent_logs)): ?>
                <tr>
                  <td>#<?php echo $log['log_id']; ?></td>
                  <td>
                    <?php 
                      $act = $log['action'];
                      $badge_class = 'badge-action';
                      if ($act === 'LOGIN') $badge_class = 'badge-login';
                      elseif ($act === 'LOGOUT') $badge_class = 'badge-logout';
                      elseif ($act === 'SYSTEM_INIT') $badge_class = 'badge-system';
                    ?>
                    <span class="badge <?php echo $badge_class; ?>">
                      <?php echo htmlspecialchars($act); ?>
                    </span>
                  </td>
                  <td><code><?php echo htmlspecialchars($log['user_email']); ?></code></td>
                  <td>
                    <span class="badge <?php echo ($log['user_role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                      <?php echo htmlspecialchars($log['user_role']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($log['details']); ?></td>
                  <td><small><?php echo htmlspecialchars($log['ip_address']); ?></small></td>
                  <td><small><?php echo htmlspecialchars($log['log_time']); ?></small></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center; color: #888; padding: 20px;">No audit log records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 14: Role-Based Access Control, Dynamic Menu & Audit Logs</p>
  </footer>

</body>
</html>
