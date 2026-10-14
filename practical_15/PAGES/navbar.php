<?php
// navbar.php - Unified Dynamic Navigation Component based on User Role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar" aria-label="Main Navigation">
  <a class="brand" href="index.php">
    🎓 StudentHub Portal
  </a>

  <div class="nav-links">
    <!-- Links common to all visitors -->
    <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">🏠 Home</a>

    <?php if ($role === 'admin'): ?>
      <!-- Admin Role Menu -->
      <a href="admin_dashboard.php" class="<?php echo ($current_page === 'admin_dashboard.php') ? 'active' : ''; ?>">📊 Dashboard</a>
      <a href="students.php" class="<?php echo ($current_page === 'students.php' || $current_page === 'add_student.php' || $current_page === 'edit_student.php') ? 'active' : ''; ?>">👥 Students</a>
      <a href="events.php" class="<?php echo ($current_page === 'events.php' || $current_page === 'add_event.php' || $current_page === 'edit_event.php') ? 'active' : ''; ?>">📅 Events</a>
      <a href="ajax_events.php" class="<?php echo ($current_page === 'ajax_events.php') ? 'active' : ''; ?>">⚡ AJAX Events</a>
      <a href="audit_logs.php" class="<?php echo ($current_page === 'audit_logs.php') ? 'active' : ''; ?>">📜 Audit Logs</a>
      <a href="dashboard.php" style="background: rgba(255, 255, 255, 0.15); border: 1px dashed #ffd8a8; font-size: 0.82rem;" title="Switch to Student view">
        🔄 Student View
      </a>
      <a href="logout.php" class="btn-logout">🚪 Logout (Admin)</a>

    <?php elseif ($role === 'student'): ?>
      <!-- Student Role Menu -->
      <a href="dashboard.php" class="<?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">🎓 My Dashboard</a>
      <a href="events.php" class="<?php echo ($current_page === 'events.php') ? 'active' : ''; ?>">📅 Browse Events</a>
      <a href="ajax_events.php" class="<?php echo ($current_page === 'ajax_events.php') ? 'active' : ''; ?>">⚡ AJAX Events</a>
      
      <!-- Security Test Link: Demonstrates RBAC blocking student from Admin routes -->
      <a href="admin_dashboard.php" style="background: rgba(231, 76, 60, 0.2); border: 1px solid #e74c3c; color: #ffcccc;" title="Test Access Control">
        🛡️ Try Admin Area
      </a>
      <a href="logout.php" class="btn-logout">🚪 Logout (Student)</a>

    <?php else: ?>
      <!-- Guest Menu -->
      <a href="events.php" class="<?php echo ($current_page === 'events.php') ? 'active' : ''; ?>">📅 Events</a>
      <a href="ajax_events.php" class="<?php echo ($current_page === 'ajax_events.php') ? 'active' : ''; ?>">⚡ AJAX Events</a>
      <a href="login.php" class="<?php echo ($current_page === 'login.php') ? 'active' : ''; ?>">🔑 Login</a>
      <a href="register.php" class="<?php echo ($current_page === 'register.php') ? 'active' : ''; ?>">📝 Register</a>
    <?php endif; ?>
  </div>
</nav>
