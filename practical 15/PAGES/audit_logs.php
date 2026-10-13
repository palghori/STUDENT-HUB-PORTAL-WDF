<?php
// audit_logs.php - Security Audit Log Trail Viewer
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$success_msg = "";
$error_msg   = "";

// Clear all logs handler
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['clear_logs'])) {
    if (mysqli_query($conn, "TRUNCATE TABLE audit_logs")) {
        record_audit_log($conn, 'CLEAR_AUDIT_LOGS', "Audit logs truncated by Admin");
        $success_msg = "All historical audit logs cleared. Audit trail refreshed.";
    } else {
        $error_msg = "Error clearing audit logs: " . mysqli_error($conn);
    }
}

// Filter query
$action_filter = trim($_GET['action_filter'] ?? '');
if (!empty($action_filter)) {
    $filter_safe = mysqli_real_escape_string($conn, $action_filter);
    $query = "SELECT * FROM audit_logs WHERE action = '$filter_safe' ORDER BY log_time DESC, log_id DESC";
} else {
    $query = "SELECT * FROM audit_logs ORDER BY log_time DESC, log_id DESC";
}
$result = mysqli_query($conn, $query);

$distinct_actions_q = mysqli_query($conn, "SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Audit Logs - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>📜 Security Audit Trail</h1>
        <p>Immutable log of authentication, CRUD operations, administrative actions and system events</p>
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
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <!-- Filter Form -->
        <form action="audit_logs.php" method="GET" class="filter-bar" style="margin-bottom: 0;">
          <label for="action_filter" style="font-weight: 600; color: #6d4423;">Filter by Action:</label>
          <select name="action_filter" id="action_filter" onchange="this.form.submit()">
            <option value="">-- All Actions --</option>
            <?php if ($distinct_actions_q): ?>
              <?php while ($act = mysqli_fetch_assoc($distinct_actions_q)): ?>
                <option value="<?php echo htmlspecialchars($act['action']); ?>" <?php echo ($action_filter === $act['action']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($act['action']); ?>
                </option>
              <?php endwhile; ?>
            <?php endif; ?>
          </select>
          <?php if (!empty($action_filter)): ?>
            <a href="audit_logs.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">Reset Filter</a>
          <?php endif; ?>
        </form>

        <!-- Clear Logs Button -->
        <form action="audit_logs.php" method="POST" onsubmit="return confirm('Clear all audit records? Action is irreversible.');">
          <input type="hidden" name="clear_logs" value="1">
          <button type="submit" class="btn btn-danger" style="font-size: 0.85rem; padding: 7px 14px;">
            🗑️ Clear All Logs
          </button>
        </form>
      </div>

      <!-- Logs Table -->
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
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><strong>#<?php echo $row['log_id']; ?></strong></td>
                  <td>
                    <span class="badge badge-action"><?php echo htmlspecialchars($row['action']); ?></span>
                  </td>
                  <td><code><?php echo htmlspecialchars($row['user_email']); ?></code></td>
                  <td>
                    <span class="badge <?php echo ($row['user_role'] === 'admin') ? 'badge-admin' : 'badge-student'; ?>">
                      <?php echo htmlspecialchars($row['user_role']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($row['details']); ?></td>
                  <td><small><?php echo htmlspecialchars($row['ip_address']); ?></small></td>
                  <td><small><?php echo htmlspecialchars($row['log_time']); ?></small></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center; color: #888; padding: 25px;">No audit records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Security Audit Log Trail</p>
  </footer>

</body>
</html>
