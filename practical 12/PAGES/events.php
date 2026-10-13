<?php
// events.php - View all events + Delete
require_once 'db_connect.php';

$success_msg = "";
$error_msg   = "";

// --- DELETE ---
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // First fetch the poster filename so we can delete the file too
    $fetch = mysqli_query($conn, "SELECT poster FROM events WHERE event_id = $id");
    $row   = mysqli_fetch_assoc($fetch);

    if (mysqli_query($conn, "DELETE FROM events WHERE event_id = $id")) {
        // Delete uploaded poster file if it exists
        if (!empty($row['poster']) && file_exists('../UPLOADS/' . $row['poster'])) {
            unlink('../UPLOADS/' . $row['poster']);
        }
        $success_msg = "Event deleted successfully!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

// --- SELECT all events ---
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Management</title>
    <link rel="stylesheet" href="../CSS/script.css" />
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px 12px; border: 1px solid #444; text-align: left; font-size: 0.88rem; vertical-align: middle; }
        th { background: #f0a060; color: #111; }
        tr:nth-child(even) { background: rgba(255,255,255,0.05); }
        .action-btns a { margin-right: 6px; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.82rem; }
        .btn-edit   { background: #4a90e2; color: #fff; }
        .btn-delete { background: #e25555; color: #fff; }
        .poster-thumb { width: 60px; height: 50px; object-fit: cover; border-radius: 4px; }
        .no-poster { color: #888; font-size: 0.8rem; }
        .msg-success { color: #4CAF50; font-weight: bold; margin-bottom: 12px; }
        .msg-error   { color: #f44336; font-weight: bold; margin-bottom: 12px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    </style>
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
    <section class="form-box" style="max-width:1100px;">
        <div class="top-bar">
            <h2>Event Management</h2>
            <a href="add_event.php" style="background:#4CAF50;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:bold;">+ Add New Event</a>
        </div>

        <?php if ($success_msg): ?>
            <p class="msg-success"><?php echo $success_msg; ?></p>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <p class="msg-error"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <?php if (!empty($row['poster']) && file_exists('../UPLOADS/' . $row['poster'])): ?>
                            <img src="../UPLOADS/<?php echo htmlspecialchars($row['poster']); ?>"
                                 class="poster-thumb" alt="Poster" />
                        <?php else: ?>
                            <span class="no-poster">No poster</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['description'], 0, 60)) . '...'; ?></td>
                    <td class="action-btns">
                        <a class="btn-edit"   href="edit_event.php?id=<?php echo $row['event_id']; ?>">Edit</a>
                        <a class="btn-delete" href="events.php?delete=<?php echo $row['event_id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="margin-top:20px;color:#aaa;">No events found. <a href="add_event.php" style="color:#f0a060;">Add one now.</a></p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
