<?php
// edit_event.php - SELECT (pre-fill) and UPDATE an event with optional new poster upload
require_once 'db_connect.php';

$message = "";
$success = false;

// Get event ID from URL
if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit();
}
$id = (int) $_GET['id'];

// Handle UPDATE when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $event_date  = mysqli_real_escape_string($conn, $_POST['event_date']);
    $old_poster  = $_POST['old_poster']; // keep existing poster by default

    if (empty($title) || empty($event_date)) {
        $message = "Error: Title and Date are required.";
    } else {

        $new_poster = $old_poster; // start with existing poster

        // Check if a new file was uploaded
        if (!empty($_FILES['poster']['name'])) {
            $file      = $_FILES['poster'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp  = $file['tmp_name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size      = 2 * 1024 * 1024; // 2MB

            if (!in_array($file_ext, $allowed_types)) {
                $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($file_size > $max_size) {
                $message = "Error: File size must not exceed 2MB.";
            } else {
                $new_poster  = time() . "_" . basename($file_name);
                $upload_path = "../UPLOADS/" . $new_poster;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Delete old poster file
                    if (!empty($old_poster) && file_exists('../UPLOADS/' . $old_poster)) {
                        unlink('../UPLOADS/' . $old_poster);
                    }
                } else {
                    $message    = "Error: Could not upload the new poster.";
                    $new_poster = $old_poster;
                }
            }
        }

        // Run UPDATE query only if no error
        if (empty($message)) {
            $update = "UPDATE events SET
                        title       = '$title',
                        category    = '$category',
                        description = '$description',
                        event_date  = '$event_date',
                        poster      = '$new_poster'
                       WHERE event_id = $id";

            if (mysqli_query($conn, $update)) {
                $success = true;
                $message = "Event updated successfully!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// SELECT current event data to pre-fill form
$fetch = mysqli_query($conn, "SELECT * FROM events WHERE event_id = $id");
$event = mysqli_fetch_assoc($fetch);

if (!$event) {
    echo "<p>Event not found.</p>";
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Event</title>
    <link rel="stylesheet" href="../CSS/script.css" />
</head>
<body>
<nav class="navbar">
    <a class="brand" href="#">StudentHub</a>
    <div class="nav-links">
        <a href="events.php">Events</a>
        <a href="add_event.php">Add Event</a>
    </div>
</nav>
<main>
    <section class="form-box">
        <h2>Edit Event</h2>

        <?php if ($message): ?>
            <p style="color:<?php echo $success ? '#4CAF50' : '#f44336'; ?>;font-weight:bold;margin-bottom:12px;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <!-- Show current poster if it exists -->
        <?php if (!empty($event['poster']) && file_exists('../UPLOADS/' . $event['poster'])): ?>
        <div style="margin-bottom:14px;">
            <p style="margin-bottom:6px;color:#aaa;font-size:0.9rem;">Current Poster:</p>
            <img src="../UPLOADS/<?php echo htmlspecialchars($event['poster']); ?>"
                 style="max-width:180px;border-radius:8px;border:2px solid #f0a060;" alt="Current Poster" />
        </div>
        <?php endif; ?>

        <form method="POST" action="edit_event.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <!-- Pass old poster name as hidden field -->
            <input type="hidden" name="old_poster" value="<?php echo htmlspecialchars($event['poster']); ?>" />

            <label>Event Title *</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required />

            <label>Category</label>
            <select name="category">
                <?php
                $categories = ['Technical','Cultural','Sports','Workshop','Seminar','Other'];
                foreach ($categories as $cat) {
                    $sel = ($event['category'] == $cat) ? 'selected' : '';
                    echo "<option value='$cat' $sel>$cat</option>";
                }
                ?>
            </select>

            <label>Event Date *</label>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required />

            <label>Description</label>
            <textarea name="description" rows="4"
                      style="width:100%;padding:10px;border-radius:6px;border:1px solid #555;background:#1a1a2e;color:#eee;resize:vertical;"
            ><?php echo htmlspecialchars($event['description']); ?></textarea>

            <label>Upload New Poster <small style="color:#aaa;">(leave blank to keep current)</small></label>
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.gif"
                   style="padding:8px;background:#1a1a2e;color:#eee;border:1px solid #555;border-radius:6px;width:100%;"/>
            <small style="color:#aaa;">Allowed: JPG, PNG, GIF | Max size: 2MB</small>

            <button type="submit" style="margin-top:16px;">Update Event</button>
        </form>
        <p style="margin-top:12px;"><a href="events.php" style="color:#f0a060;">← Back to Events</a></p>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
