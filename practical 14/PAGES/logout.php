<?php
// logout.php - Destroys user session and records audit log
session_start();
require_once 'db_connect.php';
require_once 'audit_helper.php';

// Record logout action in audit log if user was logged in
if (isset($_SESSION['role'])) {
    record_audit_log($conn, 'LOGOUT', "User " . ($_SESSION['email'] ?? 'unknown') . " logged out");
}

// Clear all session variables
$_SESSION = array();

// If session cookie exists, destroy cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: login.php?msg=logged_out");
exit();
?>
