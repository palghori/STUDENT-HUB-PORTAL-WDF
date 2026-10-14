<?php
// access_denied.php - Displays 403 Access Denied when RBAC blocks unauthorized access
session_start();
$current_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$current_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest User';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>403 - Access Denied | StudentHub Portal</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="access-denied-box">
      <div class="access-denied-icon">🚫</div>
      <h1 style="color: #c0392b; margin-top: 0; font-size: 2rem;">403 - Access Denied</h1>
      <h3 style="color: #7f1d1d; margin-top: 0;">Administrator Privileges Required</h3>
      
      <p style="color: #555; font-size: 1.05rem; margin: 20px 0;">
        You do not have permission to access the <strong>Admin Dashboard</strong> or administrative resources.
      </p>

      <div style="background: #fff5f5; border: 1px solid #fed7d7; padding: 15px; border-radius: 8px; margin-bottom: 24px; text-align: left;">
        <p style="margin: 4px 0;"><strong>Logged In As:</strong> <?php echo htmlspecialchars($current_name); ?></p>
        <p style="margin: 4px 0;">
          <strong>Detected Role:</strong> 
          <span class="badge badge-student"><?php echo htmlspecialchars(strtoupper($current_role)); ?></span>
        </p>
        <p style="margin: 4px 0; color: #742a2a; font-size: 0.9rem;">
          ℹ️ <strong>RBAC Policy:</strong> Only users with the <code>admin</code> role can access the Admin Dashboard and Audit Logs.
        </p>
      </div>

      <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <?php if ($current_role === 'student'): ?>
          <a href="student_dashboard.php" class="btn btn-primary">
            🏠 Return to Student Dashboard
          </a>
        <?php endif; ?>
        
        <a href="login.php?demo=admin" class="btn btn-outline" style="border-color: #27ae60; color: #27ae60;">
          🛡️ Switch to Admin Account
        </a>
        <a href="logout.php" class="btn btn-danger">
          🚪 Logout
        </a>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 14: Role-Based Access Control</p>
  </footer>

</body>
</html>
