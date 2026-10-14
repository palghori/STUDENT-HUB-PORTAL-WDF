<?php
// add_event.php - Admin form to schedule events with poster image upload & validation
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($category) || empty($event_date)) {
        $error_msg = "Please fill in all required event details.";
    } else {
        $poster_filename = null;

        // Handle File Upload with type and size validation
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['poster']['tmp_name'];
            $file_name = basename($_FILES['poster']['name']);
            $file_size = $_FILES['poster']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts  = ['jpg', 'jpeg', 'png', 'webp'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size      = 2 * 1024 * 1024; // 2MB

            $finfo     = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);

            if (!in_array($file_ext, $allowed_exts) || !in_array($mime_type, $allowed_mimes)) {
                $error_msg = "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
            } elseif ($file_size > $max_size) {
                $error_msg = "File too large. Maximum poster size is 2MB.";
            } else {
                $upload_dir = '../UPLOADS/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.]/', '', $file_name);
                $dest_path    = $upload_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $dest_path)) {
                    $poster_filename = $new_filename;
                } else {
                    $error_msg = "Failed to upload poster image to server.";
                }
            }
        }

        if (empty($error_msg)) {
            $title_safe = mysqli_real_escape_string($conn, $title);
            $cat_safe   = mysqli_real_escape_string($conn, $category);
            $date_safe  = mysqli_real_escape_string($conn, $event_date);
            $desc_safe  = mysqli_real_escape_string($conn, $description);
            $post_val   = $poster_filename ? "'$poster_filename'" : "NULL";

            $sql = "INSERT INTO events (title, category, description, event_date, poster) 
                    VALUES ('$title_safe', '$cat_safe', '$desc_safe', '$date_safe', $post_val)";

            if (mysqli_query($conn, $sql)) {
                record_audit_log($conn, 'ADD_EVENT', "Admin scheduled event: $title_safe");
                header("Location: events.php");
                exit();
            } else {
                $error_msg = "Database insert error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Event - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box" style="max-width: 580px;">
      <h2>➕ Schedule New Campus Event</h2>
      
      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="add_event.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Event Title:</label>
          <input type="text" name="title" class="form-control" required placeholder="e.g. AI Innovation Summit 2026">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Category:</label>
            <select name="category" class="form-control" required>
              <option value="Technical">Technical</option>
              <option value="Workshop">Workshop</option>
              <option value="Cultural">Cultural</option>
              <option value="Sports">Sports</option>
            </select>
          </div>
          <div class="form-group">
            <label>Event Date:</label>
            <input type="date" name="event_date" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label>Description:</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Brief summary of event schedule and venue..."></textarea>
        </div>

        <div class="form-group">
          <label>Event Poster Image (JPG, PNG, WEBP - Max 2MB):</label>
          <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Save Event & Upload</button>
          <a href="events.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

</body>
</html>
