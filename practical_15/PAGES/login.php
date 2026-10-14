<?php
// login.php - Secure Authentication with Password Verification and RBAC
session_start();
require_once 'db_connect.php';
require_once 'audit_helper.php';

$error_msg = "";
$info_msg  = "";

// Check for redirect messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logged_out') {
        $info_msg = "You have been logged out securely.";
    } elseif ($_GET['msg'] === 'registered') {
        $info_msg = "Registration successful! You may now log in.";
    } elseif ($_GET['msg'] === 'unauthorized') {
        $error_msg = "Access Denied: Please log in with an Administrator account.";
    }
}

// Quick 1-Click Demo Login Handler
if (isset($_GET['demo'])) {
    $demo_type = $_GET['demo'];
    $role_to_find = ($demo_type === 'admin') ? 'admin' : 'student';

    $query = "SELECT * FROM students WHERE role = '$role_to_find' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        session_regenerate_id(true);
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['full_name']  = $user['full_name'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['role']       = $user['role'];

        record_audit_log($conn, 'DEMO_LOGIN', "Quick demo login as " . $user['role'] . " (" . $user['email'] . ")");

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

// Standard Form Login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = "Please enter both your email address and password.";
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $query = "SELECT * FROM students WHERE email = '$email_safe' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['full_name']  = $user['full_name'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['role']       = $user['role'];

                record_audit_log($conn, 'LOGIN', "User logged in with role: " . $user['role']);

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error_msg = "Incorrect password. Please verify and try again.";
                record_audit_log($conn, 'FAILED_LOGIN', "Failed login attempt for email: $email_safe (Wrong Password)");
            }
        } else {
            $error_msg = "No account found registered under this email.";
            record_audit_log($conn, 'FAILED_LOGIN', "Failed login attempt for non-existent email: $email_safe");
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - StudentHub Portal (Practical 15)</title>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="form-box">
      <h2 style="text-align: center; color: #8b5e3c; margin-top: 0;">🔑 Sign In</h2>
      <p style="text-align: center; color: #7d6550; margin-bottom: 24px; font-size: 0.95rem;">
        Access your Student or Administrator portal
      </p>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <?php if (!empty($info_msg)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($info_msg); ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="email">Email Address:</label>
          <input type="email" id="email" name="email" class="form-control" required placeholder="admin@studenthub.com" />
        </div>

        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••" />
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
          Log In
        </button>
      </form>

      <p style="text-align: center; margin-top: 16px; font-size: 0.92rem;">
        New student? <a href="register.php">Create an account here</a>
      </p>

      <!-- 1-Click Fast Demo Testing -->
      <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ebd7c4;">
        <p style="font-size: 0.88rem; font-weight: 600; color: #8b5e3c; text-align: center; margin-bottom: 12px;">
          ⚡ 1-Click Demo Evaluation Logins
        </p>
        <div style="display: flex; gap: 10px;">
          <a href="login.php?demo=admin" class="btn btn-outline" style="flex: 1; font-size: 0.85rem; padding: 8px;">
            🛡️ Log In as Admin
          </a>
          <a href="login.php?demo=student" class="btn btn-outline" style="flex: 1; font-size: 0.85rem; padding: 8px;">
            🎓 Log In as Student
          </a>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

</body>
</html>
