<?php
// api.php — Simple PHP JSON API for Events
// Handles: GET (list), POST (add), PUT (update), DELETE (delete)

header("Content-Type: application/json"); // Tell browser this is JSON
require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE

// ─────────────────────────────────────────────
// GET — Fetch all events (list)
// ─────────────────────────────────────────────
if ($method === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");

    if (!$result) {
        echo json_encode(["success" => false, "message" => "Query failed"]);
        exit();
    }

    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }

    echo json_encode(["success" => true, "data" => $events]);
    exit();
}

// ─────────────────────────────────────────────
// POST — Add a new event
// ─────────────────────────────────────────────
if ($method === 'POST') {
    // Read JSON body sent from Fetch API
    $body  = json_decode(file_get_contents("php://input"), true);

    $title       = isset($body['title'])       ? mysqli_real_escape_string($conn, $body['title'])       : '';
    $category    = isset($body['category'])    ? mysqli_real_escape_string($conn, $body['category'])    : 'Other';
    $description = isset($body['description']) ? mysqli_real_escape_string($conn, $body['description']) : '';
    $event_date  = isset($body['event_date'])  ? mysqli_real_escape_string($conn, $body['event_date'])  : '';

    // Basic validation
    if (empty($title) || empty($event_date)) {
        echo json_encode(["success" => false, "message" => "Title and Date are required."]);
        exit();
    }

    $query = "INSERT INTO events (title, category, description, event_date)
              VALUES ('$title', '$category', '$description', '$event_date')";

    if (mysqli_query($conn, $query)) {
        $new_id = mysqli_insert_id($conn);
        echo json_encode(["success" => true, "message" => "Event added!", "id" => $new_id]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
    exit();
}

// ─────────────────────────────────────────────
// PUT — Update an existing event
// ─────────────────────────────────────────────
if ($method === 'PUT') {
    $body = json_decode(file_get_contents("php://input"), true);

    $id          = isset($body['event_id'])    ? (int) $body['event_id']                              : 0;
    $title       = isset($body['title'])       ? mysqli_real_escape_string($conn, $body['title'])      : '';
    $category    = isset($body['category'])    ? mysqli_real_escape_string($conn, $body['category'])   : 'Other';
    $description = isset($body['description']) ? mysqli_real_escape_string($conn, $body['description']): '';
    $event_date  = isset($body['event_date'])  ? mysqli_real_escape_string($conn, $body['event_date']) : '';

    if ($id === 0 || empty($title) || empty($event_date)) {
        echo json_encode(["success" => false, "message" => "ID, Title, and Date are required."]);
        exit();
    }

    $query = "UPDATE events SET
                title       = '$title',
                category    = '$category',
                description = '$description',
                event_date  = '$event_date'
              WHERE event_id = $id";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Event updated!"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
    exit();
}

// ─────────────────────────────────────────────
// DELETE — Delete an event by ID
// ─────────────────────────────────────────────
if ($method === 'DELETE') {
    $body = json_decode(file_get_contents("php://input"), true);
    $id   = isset($body['event_id']) ? (int) $body['event_id'] : 0;

    if ($id === 0) {
        echo json_encode(["success" => false, "message" => "Invalid event ID."]);
        exit();
    }

    if (mysqli_query($conn, "DELETE FROM events WHERE event_id = $id")) {
        echo json_encode(["success" => true, "message" => "Event deleted!"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
    exit();
}

// If none of the above matched
echo json_encode(["success" => false, "message" => "Invalid request method."]);
mysqli_close($conn);
?>
