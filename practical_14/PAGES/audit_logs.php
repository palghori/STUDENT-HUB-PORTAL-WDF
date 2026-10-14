<?php
// audit_logs.php - Audit Log Viewer with RBAC Protection
session_start();

// 1. RBAC Security Check: Admin Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// Handle Clear Logs action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['clear_logs'])) {
    // Delete logs but record that the logs were cleared
    $clear_sql = "TRUNCATE TABLE audit_logs";
    if (mysqli_query($conn, $clear_sql)) {
        record_audit_log($conn, 'CLEAR_LOGS', "All previous audit log records were cleared by Admin");
        $success_msg = "Audit logs table truncated successfully. New audit record created.";
    } else {
        $error_msg = "Failed to clear logs: " . mysqli_error($conn);
    }
}

// Filter query
$filter_action = trim($_GET['action_filter'] ?? '');
if (!empty($filter_action)) {
    $filter_safe = mysqli_real_escape_string($conn, $filter_action);
    $query = "SELECT * FROM audit_logs WHERE action = '$filter_safe' ORDER BY log_time DESC, log_id DESC";
} else {
    $query = "SELECT * FROM audit_logs ORDER BY log_time DESC, log_id DESC";
}
$result = mysqli_query($conn, $query);

// Fetch distinct actions for filter dropdown
$distinct_actions_q = mysqli_query($conn, "SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Audit Logs - Practical 14</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>📜 Security Audit Log Trail</h1>
        <p>Immutable record of all authentication, administrative and system events</p>
      </div>
      <div>
        <a href="admin_dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: gap: 15px; margin-bottom: 20px;">
        <!-- Filter Form -->
        <form action="audit_logs.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
          <label for="action_filter" style="font-weight: 600; color: #6d4423;">Filter by Action:</label>
          <select name="action_filter" id="action_filter" class="form-control" style="width: auto;" onchange="this.form.submit()">
            <option value="">-- All Actions --</option>
            <?php if ($distinct_actions_q): ?>
              <?php while ($act_row = mysqli_fetch_assoc($distinct_actions_q)): ?>
                <option value="<?php echo htmlspecialchars($act_row['action']); ?>" <?php echo ($filter_action === $act_row['action']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($act_row['action']); ?>
                </option>
              <?php endwhile; ?>
            <?php endif; ?>
          </select>
          <?php if (!empty($filter_action)): ?>
            <a href="audit_logs.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">Reset</a>
          <?php endif; ?>
        </form>

        <!-- Truncate button -->
        <form action="audit_logs.php" method="POST" onsubmit="return confirm('Are you sure you want to clear all audit logs?');">
          <input type="hidden" name="clear_logs" value="1">
          <button type="submit" class="btn btn-danger" style="font-size: 0.85rem; padding: 8px 14px;">
            🗑️ Clear All Logs
          </button>
        </form>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Action</th>
              <th>User Email</th>
              <th>Role</th>
              <th>Details</th>
              <th>IP Address</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
              <?php while ($log = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><strong>#<?php echo $log['log_id']; ?></strong></td>
                  <td>
                    <?php 
                      $act = $log['action'];
                      $badge_class = 'badge-action';
                      if ($act === 'LOGIN' || $act === 'DEMO_LOGIN') $badge_class = 'badge-login';
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
                <td colspan="7" style="text-align: center; color: #888; padding: 25px;">No audit logs matched the selected criteria.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 14: Audit Log Trail</p>
  </footer>

</body>
</html>
