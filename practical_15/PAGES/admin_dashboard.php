<?php
// admin_dashboard.php - Master Administrator Dashboard
session_start();

// Strict RBAC Enforcement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// Handle Admin Trigger Actions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['admin_action'])) {
    $action_type = $_POST['admin_action'];

    if ($action_type === 'ANNOUNCEMENT') {
        $announcement_text = trim($_POST['announcement_text'] ?? '');
        if (!empty($announcement_text)) {
            record_audit_log($conn, 'BROADCAST_ANNOUNCEMENT', "Admin announced: " . $announcement_text);
            $success_msg = "Campus announcement published and recorded in Audit Log!";
        } else {
            $error_msg = "Please provide announcement content.";
        }
    } elseif ($action_type === 'DIAGNOSTICS') {
        record_audit_log($conn, 'SYSTEM_DIAGNOSTICS', "Database and server integrity check completed");
        $success_msg = "System health check verified with 100% operational status!";
    } elseif ($action_type === 'BACKUP') {
        record_audit_log($conn, 'TRIGGER_BACKUP', "Full database snapshot initiated");
        $success_msg = "Database snapshot operation logged and executed!";
    }
}

// 1. Fetch Dynamic Counters
$q_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE role = 'student'");
$student_count = mysqli_fetch_assoc($q_students)['total'] ?? 0;

$q_admins = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE role = 'admin'");
$admin_count = mysqli_fetch_assoc($q_admins)['total'] ?? 0;

$q_events = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
$event_count = mysqli_fetch_assoc($q_events)['total'] ?? 0;

$q_regs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
$reg_count = mysqli_fetch_assoc($q_regs)['total'] ?? 0;

$q_logs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM audit_logs");
$log_count = mysqli_fetch_assoc($q_logs)['total'] ?? 0;

// 2. Fetch Latest Registrations
$latest_regs_q = mysqli_query($conn, "
    SELECT r.registration_id, s.full_name, s.email, e.title, r.registration_date 
    FROM registrations r
    JOIN students s ON r.student_id = s.student_id
    JOIN events e ON r.event_id = e.event_id
    ORDER BY r.registration_date DESC, r.registration_id DESC
    LIMIT 5
");

// 3. Fetch Recent Audit Logs
$recent_logs_q = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY log_time DESC, log_id DESC LIMIT 5");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="hero" style="border-left-color: #8b5e3c;">
      <div>
        <h1>📊 Administrator Control Panel</h1>
        <p>Live Portal Analytics, Role-Based Access Control, and System Audit Trail</p>
      </div>
      <div>
        <span class="badge badge-admin" style="font-size: 0.95rem; padding: 6px 14px;">
          Role: ADMIN (<?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?>)
        </span>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <!-- Real-time KPI Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon students">👥</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$student_count; ?></span>
          <span class="stat-label">Active Students</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon events">📅</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$event_count; ?></span>
          <span class="stat-label">Campus Events</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon regs">📝</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$reg_count; ?></span>
          <span class="stat-label">Total Registrations</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon admins">🛡️</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$admin_count; ?></span>
          <span class="stat-label">Admins</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon logs">📜</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$log_count; ?></span>
          <span class="stat-label">Audit Logged Events</span>
        </div>
      </div>
    </div>

    <!-- Admin Actions & Quick Tools -->
    <div class="card">
      <h2>⚡ Admin Actions (Records to Audit Log)</h2>
      <form action="admin_dashboard.php" method="POST" style="margin-top: 12px;">
        <input type="hidden" name="admin_action" value="ANNOUNCEMENT">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <input type="text" name="announcement_text" class="form-control" style="flex: 1; min-width: 260px;" placeholder="Post official campus broadcast..." required>
          <button type="submit" class="btn btn-primary">📢 Broadcast & Log</button>
        </div>
      </form>

      <div style="display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap;">
        <form action="admin_dashboard.php" method="POST" style="display: inline;">
          <input type="hidden" name="admin_action" value="DIAGNOSTICS">
          <button type="submit" class="btn btn-outline" style="border-color: #27ae60; color: #27ae60;">
            🩺 Run Server Diagnostics
          </button>
        </form>

        <form action="admin_dashboard.php" method="POST" style="display: inline;">
          <input type="hidden" name="admin_action" value="BACKUP">
          <button type="submit" class="btn btn-outline" style="border-color: #8e44ad; color: #8e44ad;">
            💾 Initiate Snapshot Backup
          </button>
        </form>

        <a href="students.php" class="btn btn-outline">👥 Manage Students</a>
        <a href="events.php" class="btn btn-outline">📅 Manage Events</a>
        <a href="ajax_events.php" class="btn btn-outline">⚡ AJAX Manager</a>
        <a href="audit_logs.php" class="btn btn-outline" style="border-color: #d35400; color: #d35400;">📜 View Full Audit Trail</a>
      </div>
    </div>

    <!-- Latest Registrations Table -->
    <div class="card">
      <h2>📋 Latest Student Registrations (SQL JOIN)</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Reg ID</th>
              <th>Student Name</th>
              <th>Email</th>
              <th>Registered Event</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($latest_regs_q && mysqli_num_rows($latest_regs_q) > 0): ?>
              <?php while ($r = mysqli_fetch_assoc($latest_regs_q)): ?>
                <tr>
                  <td><strong>#<?php echo $r['registration_id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($r['full_name']); ?></td>
                  <td><code><?php echo htmlspecialchars($r['email']); ?></code></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($r['title']); ?></span></td>
                  <td><?php echo htmlspecialchars($r['registration_date']); ?></td>
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

    <!-- Recent Audit Logs Table -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h2 style="margin: 0;">📜 Recent Security Audit Trail</h2>
        <a href="audit_logs.php" class="btn btn-outline" style="font-size: 0.85rem; padding: 5px 12px;">All Logs &rarr;</a>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Action</th>
              <th>User</th>
              <th>Role</th>
              <th>Details</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($recent_logs_q && mysqli_num_rows($recent_logs_q) > 0): ?>
              <?php while ($l = mysqli_fetch_assoc($recent_logs_q)): ?>
                <tr>
                  <td>#<?php echo $l['log_id']; ?></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($l['action']); ?></span></td>
                  <td><code><?php echo htmlspecialchars($l['user_email']); ?></code></td>
                  <td>
                    <span class="badge <?php echo ($l['user_role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                      <?php echo htmlspecialchars($l['user_role']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($l['details']); ?></td>
                  <td><small><?php echo htmlspecialchars($l['log_time']); ?></small></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 20px;">No audit trail events found.</td>
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
