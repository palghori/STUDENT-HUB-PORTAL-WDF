<?php
// add_event.php - INSERT a new event with optional poster upload
require_once 'db_connect.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize text fields
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $event_date  = mysqli_real_escape_string($conn, $_POST['event_date']);
    $poster_name = ""; // default: no poster

    // 2. Basic Validation
    if (empty($title) || empty($event_date)) {
        $message = "Error: Title and Date are required fields.";
    } else {

        // 3. Handle Poster Upload (if a file was selected)
        if (!empty($_FILES['poster']['name'])) {

            $file      = $_FILES['poster'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp  = $file['tmp_name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Allowed file types
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            // Max size: 2MB
            $max_size = 2 * 1024 * 1024;

            if (!in_array($file_ext, $allowed_types)) {
                $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($file_size > $max_size) {
                $message = "Error: File size must not exceed 2MB.";
            } else {
                // Create a unique filename to avoid overwriting
                $poster_name = time() . "_" . basename($file_name);
                $upload_path = "../UPLOADS/" . $poster_name;

                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $message = "Error: Could not upload the poster. Check folder permissions.";
                    $poster_name = "";
                }
            }
        }

        // 4. Insert into database (only if no upload error)
        if (empty($message)) {
            $insert = "INSERT INTO events (title, category, description, event_date, poster)
                       VALUES ('$title', '$category', '$description', '$event_date', '$poster_name')";

            if (mysqli_query($conn, $insert)) {
                $success = true;
                $message = "Event added successfully!";
            } else {
                $message = "Error: " . mysqli_error($conn);
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
    <title>Add Event</title>
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
        <h2>Add New Event</h2>

        <?php if ($message): ?>
            <p style="color:<?php echo $success ? '#4CAF50' : '#f44336'; ?>;font-weight:bold;margin-bottom:12px;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="add_event.php" enctype="multipart/form-data">

            <label>Event Title *</label>
            <input type="text" name="title" placeholder="Enter event title" required />

            <label>Category</label>
            <select name="category">
                <option value="Technical">Technical</option>
                <option value="Cultural">Cultural</option>
                <option value="Sports">Sports</option>
                <option value="Workshop">Workshop</option>
                <option value="Seminar">Seminar</option>
                <option value="Other">Other</option>
            </select>

            <label>Event Date *</label>
            <input type="date" name="event_date" required />

            <label>Description</label>
            <textarea name="description" rows="4"
                      placeholder="Brief description of the event..."
                      style="width:100%;padding:10px;border-radius:6px;border:1px solid #555;background:#1a1a2e;color:#eee;resize:vertical;"></textarea>

            <label>Upload Event Poster</label>
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.gif"
                   style="padding:8px;background:#1a1a2e;color:#eee;border:1px solid #555;border-radius:6px;width:100%;"/>
            <small style="color:#aaa;">Allowed: JPG, PNG, GIF | Max size: 2MB</small>

            <button type="submit" style="margin-top:16px;">Add Event</button>
        </form>
        <p style="margin-top:12px;"><a href="events.php" style="color:#f0a060;">← Back to Events</a></p>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
