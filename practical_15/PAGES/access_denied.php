<?php
// access_denied.php - 403 Forbidden Access Page for RBAC Enforcement
session_start();
$current_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$current_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest Visitor';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>403 Access Denied - StudentHub Portal</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="access-denied-box">
      <div style="font-size: 54px; margin-bottom: 12px;">🚫</div>
      <h1 style="color: #c0392b; margin-top: 0;">403 - Access Denied</h1>
      <h3 style="color: #742a2a; margin-top: 0;">Administrator Privileges Required</h3>

      <p style="color: #555; margin: 18px 0;">
        You do not possess the required authorization level to view this resource.
      </p>

      <div style="background: #fff5f5; border: 1px solid #fed7d7; padding: 14px; border-radius: 8px; margin-bottom: 22px; text-align: left;">
        <p style="margin: 3px 0;"><strong>Active User:</strong> <?php echo htmlspecialchars($current_name); ?></p>
        <p style="margin: 3px 0;">
          <strong>Detected Role:</strong> 
          <span class="badge badge-student"><?php echo htmlspecialchars(strtoupper($current_role)); ?></span>
        </p>
        <p style="margin: 3px 0; color: #742a2a; font-size: 0.88rem;">
          ℹ️ <strong>RBAC Policy:</strong> Access to Administrative Dashboards and Audit Logs is strictly restricted to accounts with role <code>admin</code>.
        </p>
      </div>

      <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="dashboard.php" class="btn btn-primary">🏠 Student Dashboard</a>
        <a href="login.php?demo=admin" class="btn btn-success">🛡️ Switch to Admin (Demo)</a>
        <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Role-Based Access Control</p>
  </footer>

</body>
</html>
