<?php
// Display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data_dir = __DIR__ . '/../DATA';
if (!file_exists($data_dir)) {
    mkdir($data_dir, 0777, true);
}
$file_path = $data_dir . '/contacts.csv';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $user_message = htmlspecialchars(strip_tags(trim($_POST['message'] ?? '')));
    
    // Validate
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $message = "Error: Invalid email format.";
    } elseif (empty($name) || empty($email) || empty($user_message)) {
        $message = "Error: Please fill in all required fields.";
    } else {
        // Write to CSV file
        $file_exists = file_exists($file_path);
        $handle = fopen($file_path, 'a');
        
        if ($handle) {
            // Add headers if new file
            if (!$file_exists || filesize($file_path) == 0) {
                fputcsv($handle, ['Name', 'Email', 'Message', 'Timestamp']);
            }
            
            // Add the new row
            fputcsv($handle, [$name, $email, $user_message, date('Y-m-d H:i:s')]);
            fclose($handle);
            
            $success = true;
            $message = "Message sent successfully! Data saved to CSV.";
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
    <title>Contact Status</title>
    <link rel="stylesheet" href="../CSS/script.css" />
  </head>
  <body>
    <nav class="navbar">
      <a class="brand" href="index.html">StudentHub</a>
      <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="contact.html">Contact</a>
      </div>
    </nav>
    <main>
      <section class="form-box" style="text-align: center;">
        <h2>Contact Status</h2>
        <p style="color: <?php echo $success ? '#4CAF50' : '#f44336'; ?>; font-weight: bold; font-size: 1.2rem; margin: 20px 0;">
            <?php echo htmlspecialchars($message); ?>
        </p>
        <br>
        <a href="contact.html" style="color: #f0a060; text-decoration: underline;">Back to Contact</a>
      </section>
    </main>
  </body>
</html>
