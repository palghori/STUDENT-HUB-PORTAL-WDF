<?php
// edit_event.php - Admin form to update event details & optional poster replacement
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: access_denied.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: events.php");
    exit();
}

// Fetch existing event
$fetch_q = mysqli_query($conn, "SELECT * FROM events WHERE event_id = $id LIMIT 1");
if (!$fetch_q || mysqli_num_rows($fetch_q) === 0) {
    header("Location: events.php");
    exit();
}
$event = mysqli_fetch_assoc($fetch_q);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($category) || empty($event_date)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        $new_poster = $event['poster'];

        // Handle optional new file upload
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
                $error_msg = "Invalid file type. Only JPG, PNG, and WEBP allowed.";
            } elseif ($file_size > $max_size) {
                $error_msg = "File too large. Maximum poster size is 2MB.";
            } else {
                $upload_dir = '../UPLOADS/';
                $unique_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.]/', '', $file_name);

                if (move_uploaded_file($file_tmp, $upload_dir . $unique_name)) {
                    // Remove old poster
                    if (!empty($event['poster']) && file_exists($upload_dir . $event['poster'])) {
                        unlink($upload_dir . $event['poster']);
                    }
                    $new_poster = $unique_name;
                }
            }
        }

        if (empty($error_msg)) {
            $title_safe = mysqli_real_escape_string($conn, $title);
            $cat_safe   = mysqli_real_escape_string($conn, $category);
            $date_safe  = mysqli_real_escape_string($conn, $event_date);
            $desc_safe  = mysqli_real_escape_string($conn, $description);
            $post_val   = $new_poster ? "'$new_poster'" : "NULL";

            $sql = "UPDATE events SET 
                    title = '$title_safe', 
                    category = '$cat_safe', 
                    description = '$desc_safe', 
                    event_date = '$date_safe', 
                    poster = $post_val 
                    WHERE event_id = $id";

            if (mysqli_query($conn, $sql)) {
                record_audit_log($conn, 'UPDATE_EVENT', "Admin updated event #$id: $title_safe");
                header("Location: events.php");
                exit();
            } else {
                $error_msg = "Database update error: " . mysqli_error($conn);
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
  <title>Edit Event - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box" style="max-width: 580px;">
      <h2>✏️ Edit Event #<?php echo $id; ?></h2>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="edit_event.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Event Title:</label>
          <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($event['title']); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="form-group">
            <label>Category:</label>
            <select name="category" class="form-control" required>
              <option value="Technical" <?php echo ($event['category'] === 'Technical') ? 'selected' : ''; ?>>Technical</option>
              <option value="Workshop" <?php echo ($event['category'] === 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
              <option value="Cultural" <?php echo ($event['category'] === 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
              <option value="Sports" <?php echo ($event['category'] === 'Sports') ? 'selected' : ''; ?>>Sports</option>
            </select>
          </div>
          <div class="form-group">
            <label>Event Date:</label>
            <input type="date" name="event_date" class="form-control" required value="<?php echo htmlspecialchars($event['event_date']); ?>">
          </div>
        </div>

        <div class="form-group">
          <label>Description:</label>
          <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <div class="form-group">
          <label>Replace Poster (Optional, Max 2MB):</label>
          <?php if (!empty($event['poster']) && file_exists('../UPLOADS/' . $event['poster'])): ?>
            <div style="margin-bottom: 8px;">
              <small>Current Poster:</small><br>
              <img src="../UPLOADS/<?php echo htmlspecialchars($event['poster']); ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #ccc;">
            </div>
          <?php endif; ?>
          <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Update Event Record</button>
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
