
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'], $_POST['name'], $_POST['date'], $_POST['phone'], $_POST['address'])) {
            $email = sanitize($_POST['email']);
            $password = sanitize($_POST['password']);
            $name = sanitize($_POST['name']);
            $date = sanitize($_POST['date']);
            $phone = sanitize($_POST['phone']);
            $address = $_POST['address'] ? sanitize($_POST['address']) : null;

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  echo json_encode(['error' => 'Invalid email format!']);
                  exit;
            }

            // Validate password
            if (strlen($password) < 8) {
                  echo json_encode(['error' => 'Password must be at least 8 characters!']);
                  exit;
            } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $password)) {
                  echo json_encode(['error' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                  exit;
            }

            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  echo json_encode(['error' => 'MySQL Connection Failed!']);
                  exit;
            }

            // Using prepare statement (preventing SQL injection)


            // Close connection
            $conn->close();
      } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>