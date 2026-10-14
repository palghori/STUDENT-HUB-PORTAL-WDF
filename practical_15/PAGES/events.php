<?php
// events.php - Event Management CRUD with Poster Previews & Role-Based Actions
session_start();
require_once 'db_connect.php';
require_once 'audit_helper.php';

$current_role = $_SESSION['role'] ?? 'guest';
$student_id   = (int)($_SESSION['student_id'] ?? 0);

$success_msg = "";
$error_msg   = "";

// Admin Only: DELETE Operation
if (isset($_GET['delete']) && $current_role === 'admin') {
    $id = (int)$_GET['delete'];

    // Fetch poster filename for deletion
    $fetch_q = mysqli_query($conn, "SELECT title, poster FROM events WHERE event_id = $id LIMIT 1");
    if ($fetch_q && mysqli_num_rows($fetch_q) === 1) {
        $row = mysqli_fetch_assoc($fetch_q);

        if (mysqli_query($conn, "DELETE FROM events WHERE event_id = $id")) {
            if (!empty($row['poster']) && file_exists('../UPLOADS/' . $row['poster'])) {
                unlink('../UPLOADS/' . $row['poster']);
            }
            record_audit_log($conn, 'DELETE_EVENT', "Admin deleted event #$id: " . $row['title']);
            $success_msg = "Event deleted successfully!";
        } else {
            $error_msg = "Error deleting event: " . mysqli_error($conn);
        }
    }
}

// Student: Register for Event
if (isset($_GET['register']) && $current_role === 'student' && $student_id > 0) {
    $evt_id = (int)$_GET['register'];
    $check_q = mysqli_query($conn, "SELECT * FROM registrations WHERE student_id = $student_id AND event_id = $evt_id");
    if (mysqli_num_rows($check_q) == 0) {
        if (mysqli_query($conn, "INSERT INTO registrations (student_id, event_id) VALUES ($student_id, $evt_id)")) {
            record_audit_log($conn, 'EVENT_REGISTRATION', "Student enrolled in event #$evt_id");
            $success_msg = "You have registered for this event successfully!";
        }
    } else {
        $error_msg = "You are already registered for this event.";
    }
}

// Fetch all events
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campus Events - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>📅 Campus Events & Workshops</h1>
        <p>Explore university hackathons, seminars, and cultural festivals</p>
      </div>
      <div>
        <?php if ($current_role === 'admin'): ?>
          <a href="add_event.php" class="btn btn-primary">➕ Schedule New Event</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Poster</th>
              <th>Title</th>
              <th>Category</th>
              <th>Date</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
              <?php while ($e = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td style="width: 70px; text-align: center;">
                    <?php if (!empty($e['poster']) && file_exists('../UPLOADS/' . $e['poster'])): ?>
                      <img src="../UPLOADS/<?php echo htmlspecialchars($e['poster']); ?>" alt="Poster" class="table-thumb" />
                    <?php else: ?>
                      <span style="font-size: 1.8rem;">🖼️</span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?php echo htmlspecialchars($e['title']); ?></strong></td>
                  <td><span class="badge badge-action"><?php echo htmlspecialchars($e['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                  <td><?php echo htmlspecialchars($e['description']); ?></td>
                  <td>
                    <?php if ($current_role === 'admin'): ?>
                      <a href="edit_event.php?id=<?php echo $e['event_id']; ?>" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">✏️ Edit</a>
                      <a href="events.php?delete=<?php echo $e['event_id']; ?>" 
                         class="btn btn-danger" 
                         style="padding: 4px 8px; font-size: 0.8rem;" 
                         onclick="return confirm('Delete this event? File and record will be removed.');">
                        🗑️ Delete
                      </a>
                    <?php elseif ($current_role === 'student'): ?>
                      <a href="events.php?register=<?php echo $e['event_id']; ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8rem;">
                        Register
                      </a>
                    <?php else: ?>
                      <a href="login.php" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">
                        Login to Register
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 25px;">No campus events scheduled.</td>
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
