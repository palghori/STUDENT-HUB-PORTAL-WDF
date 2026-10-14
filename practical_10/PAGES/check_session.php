<?php
// check_session.php - Protects pages from being accessed without login
// Include this file at the top of any page that requires login

session_start();

// Session Timeout: 15 minutes
$timeout = 900;

if (!isset($_SESSION['student_id'])) {
    // User is not logged in — redirect to login page
    header("Location: login.html?msg=pleaselogin");
    exit();
}

if ((time() - $_SESSION['last_active']) > $timeout) {
    // Session has timed out
    session_destroy();
    header("Location: login.html?msg=timeout");
    exit();
}

// Update the last active time so timeout resets on activity
$_SESSION['last_active'] = time();
?>
