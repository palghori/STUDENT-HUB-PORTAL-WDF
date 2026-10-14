<?php
// dashboard.php - Student Portal Dashboard
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$student_id = (int)($_SESSION['student_id'] ?? 0);
$student_name = $_SESSION['full_name'] ?? 'Student';
$student_email = $_SESSION['email'] ?? '';
$student_role = $_SESSION['role'] ?? 'student';

$msg = "";

// Handle Quick Event Registration
if (isset($_GET['register_event'])) {
    $event_id = (int)$_GET['register_event'];
    if ($student_id > 0 && $event_id > 0) {
        $check = mysqli_query($conn, "SELECT * FROM registrations WHERE student_id = $student_id AND event_id = $event_id");
        if (mysqli_num_rows($check) == 0) {
            $insert_reg = "INSERT INTO registrations (student_id, event_id) VALUES ($student_id, $event_id)";
            if (mysqli_query($conn, $insert_reg)) {
                record_audit_log($conn, 'STUDENT_EVENT_REG', "Student #$student_id registered for event #$event_id");
                $msg = "Successfully registered for the event!";
            }
        } else {
            $msg = "You are already registered for this event.";
        }
    }
}

// Fetch Student Profile Data
$profile_q = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $student_id LIMIT 1");
$profile = mysqli_fetch_assoc($profile_q);

// Fetch Student's Registered Events
$my_events_q = mysqli_query($conn, "
    SELECT e.event_id, e.title, e.category, e.event_date, r.registration_date 
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    WHERE r.student_id = $student_id
    ORDER BY r.registration_date DESC
");

// Fetch All Available Events
$all_events_q = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard - StudentHub Portal</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="hero">
      <div>
        <h1>🎓 Welcome, <?php echo htmlspecialchars($student_name); ?>!</h1>
        <p>Your central student portal for course details, campus events, and registration history.</p>
      </div>
      <div>
        <span class="badge badge-student" style="font-size: 0.95rem; padding: 6px 14px;">
          Role: <?php echo htmlspecialchars(strtoupper($student_role)); ?>
        </span>
      </div>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-info">ℹ️ <?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- RBAC Security Demonstration Box -->
    <div class="card" style="border: 2px dashed #e74c3c; background: #fffdfd;">
      <h2 style="color: #c0392b;">🛡️ Role-Based Access Control (RBAC) Demonstration</h2>
      <p style="color: #666; font-size: 0.95rem;">
        You are authenticated with standard <code>student</code> permissions. Click the test buttons below to verify that administrative routes are locked:
      </p>
      <div style="display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;">
        <a href="admin_dashboard.php" class="btn btn-danger">
          ⚠️ Try Accessing Admin Dashboard (admin_dashboard.php)
        </a>
        <a href="audit_logs.php" class="btn btn-outline" style="border-color: #c0392b; color: #c0392b;">
          ⚠️ Try Accessing Audit Logs (audit_logs.php)
        </a>
        <a href="login.php?demo=admin" class="btn btn-success">
          🔄 Switch to Admin Account (Demo)
        </a>
      </div>
    </div>

    <!-- My Registered Events -->
    <div class="card">
      <h2>📝 My Enrolled Events</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Event Title</th>
              <th>Category</th>
              <th>Event Date</th>
              <th>Enrolled On</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($my_events_q && mysqli_num_rows($my_events_q) > 0): ?>
              <?php while ($my_evt = mysqli_fetch_assoc($my_events_q)): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($my_evt['title']); ?></strong></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($my_evt['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($my_evt['event_date']); ?></td>
                  <td><?php echo htmlspecialchars($my_evt['registration_date']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align: center; color: #888; padding: 20px;">
                  You have not registered for any events yet. Check out the available events below!
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Available Events -->
    <div class="card">
      <h2>📅 Available Campus Events</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Event Title</th>
              <th>Category</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($all_events_q && mysqli_num_rows($all_events_q) > 0): ?>
              <?php while ($evt = mysqli_fetch_assoc($all_events_q)): ?>
                <tr>
                  <td>#<?php echo $evt['event_id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($evt['title']); ?></strong></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($evt['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($evt['event_date']); ?></td>
                  <td>
                    <a href="dashboard.php?register_event=<?php echo $evt['event_id']; ?>" class="btn btn-success" style="font-size: 0.82rem; padding: 4px 10px;">
                      Enroll Now
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; color: #888; padding: 20px;">No events listed yet.</td>
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
