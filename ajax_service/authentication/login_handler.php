
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'])) {
            $email = sanitize($_POST['email']);
            $password = sanitize($_POST['password']);

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                  echo json_encode(['error' => 'Invalid email format!']);
                  exit;
            }

            // Validate password
            if (strlen($password) < 8) {
                  echo json_encode(['error' => 'Password must be at least 8 characters!']);
                  exit;
            }

            // Need to make a query to the database to continue or not

            // $response = ['email' => $email, 'password' => $password];
            // echo json_encode($response);
      } else {
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      echo json_encode(['error' => 'Invalid request method!']);
}
?>