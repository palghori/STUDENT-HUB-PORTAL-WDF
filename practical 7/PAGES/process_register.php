<?php
// Display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data_dir = __DIR__ . '/../DATA';
if (!file_exists($data_dir)) {
    mkdir($data_dir, 0777, true);
}
$file_path = $data_dir . '/registrations.json';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
    $enrollment = htmlspecialchars(strip_tags(trim($_POST['enrollment'] ?? '')));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mobile = htmlspecialchars(strip_tags(trim($_POST['mobile'] ?? '')));
    $course = htmlspecialchars(strip_tags(trim($_POST['course'] ?? '')));
    $year = htmlspecialchars(strip_tags(trim($_POST['year'] ?? '')));
    $gender = htmlspecialchars(strip_tags(trim($_POST['gender'] ?? '')));
    
    // Validate
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $message = "Error: Invalid email format.";
    } elseif (empty($name) || empty($enrollment) || empty($email) || empty($mobile)) {
        $message = "Error: Please fill in all required fields.";
    } else {
        // Read existing JSON data
        $records = [];
        if (file_exists($file_path)) {
            $json_data = file_get_contents($file_path);
            if ($json_data) {
                $records = json_decode($json_data, true) ?? [];
            }
        }

        // Add new record
        $new_record = [
            "name" => $name,
            "enrollment" => $enrollment,
            "email" => $email,
            "mobile" => $mobile,
            "course" => $course,
            "year" => $year,
            "gender" => $gender,
            "timestamp" => date('Y-m-d H:i:s')
        ];
        
        $records[] = $new_record;
        
        // Save back to JSON file
        if (file_put_contents($file_path, json_encode($records, JSON_PRETTY_PRINT))) {
            $success = true;
            $message = "Registration successful! Data saved to JSON.";
        } else {
            $message = "Error: Could not save data. Please check folder permissions.";
        }
    }
} else {
    $message = "Error: Invalid request method.";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Status</title>
    <link rel="stylesheet" href="../CSS/script.css" />
  </head>
  <body>
    <nav class="navbar">
      <a class="brand" href="index.html">StudentHub</a>
      <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="register.html">Register</a>
      </div>
    </nav>
    <main>
      <section class="form-box" style="text-align: center;">
        <h2>Registration Status</h2>
        <p style="color: <?php echo $success ? '#4CAF50' : '#f44336'; ?>; font-weight: bold; font-size: 1.2rem; margin: 20px 0;">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <br>
        <a href="register.html" style="color: #f0a060; text-decoration: underline;">Back to Register</a>
      </section>
    </main>
  </body>
</html>
