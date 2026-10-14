<?php
// navbar.php - Dynamic Menu based on User Role (RBAC)

// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$current_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
  <a class="brand" href="<?php echo ($current_role === 'admin') ? 'admin_dashboard.php' : (($current_role === 'student') ? 'student_dashboard.php' : 'login.php'); ?>">
    🎓 StudentHub Portal
  </a>

  <div class="nav-links">
    <?php if ($current_role === 'admin'): ?>
      <!-- Admin Specific Dynamic Menu -->
      <a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">📊 Dashboard</a>
      <a href="manage_students.php" class="<?php echo ($current_page == 'manage_students.php') ? 'active' : ''; ?>">👥 Students</a>
      <a href="manage_events.php" class="<?php echo ($current_page == 'manage_events.php') ? 'active' : ''; ?>">📅 Events</a>
      <a href="audit_logs.php" class="<?php echo ($current_page == 'audit_logs.php') ? 'active' : ''; ?>">📜 Audit Logs</a>
      
      <!-- Quick Demo Role Switcher -->
      <a href="student_dashboard.php" style="background: rgba(255, 255, 255, 0.15); border: 1px dashed #ffd8a8; font-size: 0.85rem;" title="Test Student View">
        🔄 Student View
      </a>
      
      <a href="logout.php" class="btn-logout" title="Sign out of Admin session">
        🚪 Logout (Admin)
      </a>

    <?php elseif ($current_role === 'student'): ?>
      <!-- Student Specific Dynamic Menu -->
      <a href="student_dashboard.php" class="<?php echo ($current_page == 'student_dashboard.php') ? 'active' : ''; ?>">🏠 Student Home</a>
      <a href="manage_events.php" class="<?php echo ($current_page == 'manage_events.php') ? 'active' : ''; ?>">📅 View Events</a>
      
      <!-- RBAC Test Link: Allows testing that students are blocked from Admin Dashboard -->
      <a href="admin_dashboard.php" style="background: rgba(231, 76, 60, 0.2); border: 1px solid #e74c3c; color: #ffcccc;" title="Test Access Control Restriction">
        🛡️ Try Admin Area
      </a>
      
      <a href="logout.php" class="btn-logout" title="Sign out of Student session">
        🚪 Logout (Student)
      </a>

    <?php else: ?>
      <!-- Guest Menu -->
      <a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">🔑 Login</a>
    <?php endif; ?>
  </div>
</nav>
