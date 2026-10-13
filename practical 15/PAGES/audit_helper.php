<?php
// audit_helper.php - Records actions into audit_logs table

function record_audit_log($conn, $action, $details = '') {
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : 'guest@studenthub.com';
    $role  = isset($_SESSION['role'])  ? $_SESSION['role']  : 'guest';
    $ip    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

    $action_safe  = mysqli_real_escape_string($conn, $action);
    $details_safe = mysqli_real_escape_string($conn, $details);
    $email_safe   = mysqli_real_escape_string($conn, $email);
    $role_safe    = mysqli_real_escape_string($conn, $role);

    $sql = "INSERT INTO audit_logs (user_email, user_role, action, details, ip_address) 
            VALUES ('$email_safe', '$role_safe', '$action_safe', '$details_safe', '$ip')";

    return mysqli_query($conn, $sql);
}
?>
