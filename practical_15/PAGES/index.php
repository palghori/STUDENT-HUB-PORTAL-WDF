<?php
// index.php - StudentHub Integrated Portal Homepage
session_start();
require_once 'db_connect.php';

// Fetch live statistics
$q_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE role = 'student'");
$students_count = mysqli_fetch_assoc($q_students)['total'] ?? 0;

$q_events = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
$events_count = mysqli_fetch_assoc($q_events)['total'] ?? 0;

$q_regs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
$regs_count = mysqli_fetch_assoc($q_regs)['total'] ?? 0;

$user_role = $_SESSION['role'] ?? 'guest';
$user_name = $_SESSION['full_name'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>StudentHub Portal - Final Integrated Web Portal</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <!-- Hero Banner -->
    <section class="hero">
      <div>
        <h1>🎓 Welcome to StudentHub Portal</h1>
        <p>
          The unified campus web application integrating Student Management, Event Administration,
          Fetch API CRUD, Role-Based Access Control (RBAC), and persistent Security Audit Logs.
        </p>
      </div>
      <div class="hero-actions">
        <?php if ($user_role === 'admin'): ?>
          <a href="admin_dashboard.php" class="btn btn-primary">📊 Open Admin Dashboard</a>
          <a href="students.php" class="btn btn-outline">👥 Manage Students</a>
        <?php elseif ($user_role === 'student'): ?>
          <a href="dashboard.php" class="btn btn-primary">🎓 My Student Portal</a>
          <a href="events.php" class="btn btn-outline">📅 Browse Events</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">🔑 Sign In</a>
          <a href="register.php" class="btn btn-outline">📝 Student Register</a>
        <?php endif; ?>
      </div>
    </section>

    <!-- Dynamic Metrics Counter -->
    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon students">👥</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$students_count; ?></span>
          <span class="stat-label">Active Students</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon events">📅</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$events_count; ?></span>
          <span class="stat-label">Campus Events</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon regs">📝</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo (int)$regs_count; ?></span>
          <span class="stat-label">Total Registrations</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon logs">⚡</div>
        <div class="stat-info">
          <span class="stat-number">100%</span>
          <span class="stat-label">Integrated Modules</span>
        </div>
      </div>
    </section>

    <!-- Feature Modules Grid -->
    <section class="card">
      <h2>🚀 Integrated Portal Modules</h2>
      <div class="features-grid">
        <div class="feature-card">
          <h3>👥 Student Management</h3>
          <p>Complete CRUD operations with real-time MySQLi search by student name/email and course filtering.</p>
          <a href="students.php" class="btn btn-outline">Explore Students &rarr;</a>
        </div>

        <div class="feature-card">
          <h3>📅 Event Management</h3>
          <p>Event scheduling with image poster file upload, mime-type and size validation, and thumbnail previews.</p>
          <a href="events.php" class="btn btn-outline">View Events &rarr;</a>
        </div>

        <div class="feature-card">
          <h3>⚡ AJAX / Fetch CRUD</h3>
          <p>Modern Single-Page Application (SPA) experience powered by PHP JSON API without full page reloads.</p>
          <a href="ajax_events.php" class="btn btn-outline">Try AJAX CRUD &rarr;</a>
        </div>

        <div class="feature-card">
          <h3>🛡️ RBAC & Audit Logs</h3>
          <p>Multi-role authorization dividing Admin from Student with persistent security audit log recording.</p>
          <a href="admin_dashboard.php" class="btn btn-outline">Admin Panel &rarr;</a>
        </div>
      </div>
    </section>

    <!-- Quick Demo Accounts -->
    <section class="card" style="background: #fffdf9; border-left: 6px solid #27ae60;">
      <h2 style="color: #27ae60;">⚡ One-Click Testing & Demo Credentials</h2>
      <p style="color: #666; margin-bottom: 15px;">
        Use the preconfigured test accounts below or use the 1-click login buttons on the sign-in page:
      </p>
      <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="background: #ffffff; padding: 15px 20px; border-radius: 8px; border: 1px solid #e2e8f0; flex: 1; min-width: 250px;">
          <h4 style="margin: 0 0 6px; color: #b9380a;">🛡️ Administrator Account</h4>
          <p style="margin: 2px 0; font-size: 0.9rem;">Email: <code>admin@studenthub.com</code></p>
          <p style="margin: 2px 0; font-size: 0.9rem;">Password: <code>admin123</code></p>
        </div>
        <div style="background: #ffffff; padding: 15px 20px; border-radius: 8px; border: 1px solid #e2e8f0; flex: 1; min-width: 250px;">
          <h4 style="margin: 0 0 6px; color: #1565c0;">🎓 Student Account</h4>
          <p style="margin: 2px 0; font-size: 0.9rem;">Email: <code>aarav@studenthub.com</code></p>
          <p style="margin: 2px 0; font-size: 0.9rem;">Password: <code>student123</code></p>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15: Final Integration, Testing, Documentation & Deployment</p>
  </footer>

</body>
</html>
