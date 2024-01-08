
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'], $_POST['confirmPassword'], $_POST['type'])) {
            try {
                  $email = sanitize($_POST['email']);
                  $password = sanitize($_POST['password']);
                  $confirmPassword = sanitize($_POST['confirmPassword']);
                  $user_type= sanitize($_POST['type']);

                  if (!$email) {
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  if (!$password) {
                        echo json_encode(['error' => 'No new password provided!']);
                        exit;
                  } else if (strlen($password) < 8) {
                        echo json_encode(['error' => 'New password must be at least 8 characters!']);
                        exit;
                  } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $password)) {
                        echo json_encode(['error' => 'New password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                        exit;
                  }

                  if (!$confirmPassword) {
                        echo json_encode(['error' => 'No confirm password provided!']);
                        exit;
                  } else if (strlen($confirmPassword) < 8) {
                        echo json_encode(['error' => 'Confirm password must be at least 8 characters!']);
                        exit;
                  } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $confirmPassword)) {
                        echo json_encode(['error' => 'Confirm password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                        exit;
                  }

                  if ($confirmPassword !== $password) {
                        echo json_encode(['error' => 'Passwords are not matched!']);
                        exit;
                  }

                  // Valid user type
                  if (!$user_type) {
                        echo json_encode(['error' => 'No user type provided!']);
                        exit;
                  } else if ($user_type !== 'admin' && $user_type !== 'customer') {
                        echo json_encode(['error' => 'Invalid user type!']);
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

                  $hashedPassword = hash_password($password);
                  // Using prepare statement (preventing SQL injection)
                  $stmt = $conn->prepare("UPDATE appUser SET password=? WHERE email=?");
                  $stmt->bind_param('ss', $hashedPassword, $email);
                  $stmt->execute();

                  if ($stmt->affected_rows <= 0) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        echo json_encode(['query_result' => true]);
                        change_password_mail($email, $user_type);
                  }

                  // Close statement
                  $stmt->close();

                  // Close connection
                  $conn->close();
            } catch (Exception $e) {
                  http_response_code(500);
                  echo json_encode(['error' => $e->getMessage()]);
            }
      } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>