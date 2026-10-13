<?php
// logout.php - Session destruction with audit trail
session_start();
require_once 'db_connect.php';
require_once 'audit_helper.php';

if (isset($_SESSION['role'])) {
    record_audit_log($conn, 'LOGOUT', "User " . ($_SESSION['email'] ?? 'unknown') . " logged out");
}

$_SESSION = array();

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

session_destroy();

header("Location: login.php?msg=logged_out");
exit();
?>
