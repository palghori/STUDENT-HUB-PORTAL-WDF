<?php
// manage_events.php - Events management with Role-Based capabilities & Audit Logging
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$current_role = $_SESSION['role'];
$success_msg  = "";
$error_msg    = "";

// Admin only: Handle Add Event
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_event']) && $current_role === 'admin') {
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $date     = trim($_POST['event_date'] ?? '');

    if (!empty($title) && !empty($category) && !empty($date)) {
        $title_safe    = mysqli_real_escape_string($conn, $title);
        $category_safe = mysqli_real_escape_string($conn, $category);
        $desc_safe     = mysqli_real_escape_string($conn, $desc);
        $date_safe     = mysqli_real_escape_string($conn, $date);

        $sql = "INSERT INTO events (title, category, description, event_date) VALUES ('$title_safe', '$category_safe', '$desc_safe', '$date_safe')";
        if (mysqli_query($conn, $sql)) {
            record_audit_log($conn, 'ADD_EVENT', "Created event: " . $title);
            $success_msg = "Event '$title' added and logged in Audit Log!";
        } else {
            $error_msg = "Error adding event: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Title, Category, and Event Date are required.";
    }
}

// Admin only: Handle Delete Event
if (isset($_GET['delete_event']) && $current_role === 'admin') {
    $event_id = (int)$_GET['delete_event'];
    
    // Get title for logging
    $info_q = mysqli_query($conn, "SELECT title FROM events WHERE event_id = $event_id LIMIT 1");
    if ($info_q && mysqli_num_rows($info_q) === 1) {
        $event_info = mysqli_fetch_assoc($info_q);
        $event_title = $event_info['title'];

        $del_sql = "DELETE FROM events WHERE event_id = $event_id";
        if (mysqli_query($conn, $del_sql)) {
            record_audit_log($conn, 'DELETE_EVENT', "Deleted event #$event_id: " . $event_title);
            $success_msg = "Event deleted and logged in Audit Log!";
        }
    }
}

// Student: Handle Event Registration
if (isset($_GET['register_event']) && $current_role === 'student') {
    $event_id = (int)$_GET['register_event'];
    $student_id = (int)($_SESSION['student_id'] ?? 0);

    if ($student_id > 0) {
        // Check if already registered
        $check = mysqli_query($conn, "SELECT * FROM registrations WHERE student_id = $student_id AND event_id = $event_id");
        if (mysqli_num_rows($check) == 0) {
            $reg_sql = "INSERT INTO registrations (student_id, event_id) VALUES ($student_id, $event_id)";
            if (mysqli_query($conn, $reg_sql)) {
                record_audit_log($conn, 'EVENT_REGISTRATION', "Student registered for event #$event_id");
                $success_msg = "You have successfully registered for this event!";
            }
        } else {
            $error_msg = "You are already registered for this event.";
        }
    }
}

// Fetch all events
$result_events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Events - Practical 14</title>
  <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>📅 Campus Events</h1>
        <p>
          <?php if ($current_role === 'admin'): ?>
            Full administrative control: Add, review, or delete campus events.
          <?php else: ?>
            Browse and register for upcoming university events.
          <?php endif; ?>
        </p>
      </div>
      <div>
        <a href="<?php echo ($current_role === 'admin') ? 'admin_dashboard.php' : 'student_dashboard.php'; ?>" class="btn btn-outline">&larr; Return to Dashboard</a>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <?php if ($current_role === 'admin'): ?>
      <!-- Admin Only: Add Event Form -->
      <div class="card">
        <h2>➕ Create New Event (Admin Action)</h2>
        <form action="manage_events.php" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; align-items: flex-end;">
          <input type="hidden" name="add_event" value="1">
          <div class="form-group" style="margin-bottom: 0;">
            <label>Event Title:</label>
            <input type="text" name="title" class="form-control" required placeholder="e.g. AI Innovation Summit">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label>Category:</label>
            <select name="category" class="form-control" required>
              <option value="Technical">Technical</option>
              <option value="Cultural">Cultural</option>
              <option value="Sports">Sports</option>
              <option value="Workshop">Workshop</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label>Event Date:</label>
            <input type="date" name="event_date" class="form-control" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label>Description:</label>
            <input type="text" name="description" class="form-control" placeholder="Short description...">
          </div>
          <div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create & Log</button>
          </div>
        </form>
      </div>
    <?php endif; ?>

    <!-- Events List Table -->
    <div class="card">
      <h2>All Events</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Category</th>
              <th>Date</th>
              <th>Description</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_events && mysqli_num_rows($result_events) > 0): ?>
              <?php while ($evt = mysqli_fetch_assoc($result_events)): ?>
                <tr>
                  <td>#<?php echo $evt['event_id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($evt['title']); ?></strong></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($evt['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($evt['event_date']); ?></td>
                  <td><?php echo htmlspecialchars($evt['description']); ?></td>
                  <td>
                    <?php if ($current_role === 'admin'): ?>
                      <a href="manage_events.php?delete_event=<?php echo $evt['event_id']; ?>" 
                         class="btn btn-danger" 
                         style="font-size: 0.8rem; padding: 4px 10px;"
                         onclick="return confirm('Are you sure you want to delete this event? Action will be recorded in audit log.');">
                        Delete
                      </a>
                    <?php else: ?>
                      <a href="manage_events.php?register_event=<?php echo $evt['event_id']; ?>" 
                         class="btn btn-success" 
                         style="font-size: 0.8rem; padding: 4px 10px;">
                        Register
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 20px;">No events registered yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 14: Events Management</p>
  </footer>

</body>
</html>
