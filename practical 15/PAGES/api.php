<?php
// api.php - RESTful PHP JSON API for AJAX Operations in Practical 15
header("Content-Type: application/json");
require_once 'db_connect.php';
require_once 'audit_helper.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'];

// 1. GET - Fetch list of events
if ($method === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");

    if (!$result) {
        echo json_encode(["success" => false, "message" => "Database query failed: " . mysqli_error($conn)]);
        exit();
    }

    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }

    echo json_encode(["success" => true, "data" => $events]);
    exit();
}

// 2. POST - Add new event
if ($method === 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);

    $title       = trim($body['title'] ?? '');
    $category    = trim($body['category'] ?? 'Technical');
    $description = trim($body['description'] ?? '');
    $event_date  = trim($body['event_date'] ?? '');

    if (empty($title) || empty($event_date)) {
        echo json_encode(["success" => false, "message" => "Title and event date are required."]);
        exit();
    }

    $title_safe = mysqli_real_escape_string($conn, $title);
    $cat_safe   = mysqli_real_escape_string($conn, $category);
    $desc_safe  = mysqli_real_escape_string($conn, $description);
    $date_safe  = mysqli_real_escape_string($conn, $event_date);

    $sql = "INSERT INTO events (title, category, description, event_date) VALUES ('$title_safe', '$cat_safe', '$desc_safe', '$date_safe')";

    if (mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        record_audit_log($conn, 'AJAX_ADD_EVENT', "Added event via Fetch API: $title_safe");
        echo json_encode(["success" => true, "message" => "Event added successfully via AJAX!", "id" => $id]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add event: " . mysqli_error($conn)]);
    }
    exit();
}

// 3. PUT - Update event
if ($method === 'PUT') {
    $body = json_decode(file_get_contents("php://input"), true);

    $id          = (int)($body['event_id'] ?? 0);
    $title       = trim($body['title'] ?? '');
    $category    = trim($body['category'] ?? '');
    $description = trim($body['description'] ?? '');
    $event_date  = trim($body['event_date'] ?? '');

    if ($id <= 0 || empty($title) || empty($event_date)) {
        echo json_encode(["success" => false, "message" => "Valid Event ID, title, and date are required."]);
        exit();
    }

    $title_safe = mysqli_real_escape_string($conn, $title);
    $cat_safe   = mysqli_real_escape_string($conn, $category);
    $desc_safe  = mysqli_real_escape_string($conn, $description);
    $date_safe  = mysqli_real_escape_string($conn, $event_date);

    $sql = "UPDATE events SET title='$title_safe', category='$cat_safe', description='$desc_safe', event_date='$date_safe' WHERE event_id=$id";

    if (mysqli_query($conn, $sql)) {
        record_audit_log($conn, 'AJAX_UPDATE_EVENT', "Updated event #$id via Fetch API: $title_safe");
        echo json_encode(["success" => true, "message" => "Event updated successfully via AJAX!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . mysqli_error($conn)]);
    }
    exit();
}

// 4. DELETE - Delete event
if ($method === 'DELETE') {
    $body = json_decode(file_get_contents("php://input"), true);
    $id   = (int)($body['event_id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "Valid Event ID is required for deletion."]);
        exit();
    }

    if (mysqli_query($conn, "DELETE FROM events WHERE event_id = $id")) {
        record_audit_log($conn, 'AJAX_DELETE_EVENT', "Deleted event #$id via Fetch API");
        echo json_encode(["success" => true, "message" => "Event deleted successfully via AJAX!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Deletion failed: " . mysqli_error($conn)]);
    }
    exit();
}

echo json_encode(["success" => false, "message" => "Unsupported HTTP Request Method"]);
?>
