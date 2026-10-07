<?php
// logout.php - Destroys the session and logs the user out

// 1. Start the session so we can access and destroy it
session_start();

// 2. Clear all session variables
$_SESSION = array();

// 3. Destroy the session completely
session_destroy();

// 4. Delete the remember-me cookie if it exists
if (isset($_COOKIE['remember_email'])) {
    setcookie("remember_email", "", time() - 3600, "/");
}

// 5. Redirect the user back to the login page
header("Location: login.html?msg=loggedout");
exit();
?>
