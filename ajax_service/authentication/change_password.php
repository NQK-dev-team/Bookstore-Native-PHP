
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'], $_POST['confirmPassword'], $_POST['type'])) {
            try {
                  $email = sanitize(rawurldecode($_POST['email']));
                  $password = sanitize(rawurldecode($_POST['password']));
                  $confirmPassword = sanitize(rawurldecode($_POST['confirmPassword']));
                  $user_type = sanitize(rawurldecode($_POST['type']));

                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  if (!$password) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No new password provided!']);
                        exit;
                  } else if (strlen($password) < 8) {
                        http_response_code(400);
                        echo json_encode(['error' => 'New password must be at least 8 characters long!']);
                        exit;
                  } else {
                        $matchResult = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $password);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during password format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'New password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                              exit;
                        }
                  }

                  if (!$confirmPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No confirm password provided!']);
                        exit;
                  } else if (strlen($confirmPassword) < 8) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Confirm password must be at least 8 characters long!']);
                        exit;
                  }  else {
                        $matchResult = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $confirmPassword);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during confirm password format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Confirm password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                              exit;
                        }
                  }

                  if ($confirmPassword !== $password) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Passwords are not matched!']);
                        exit;
                  }

                  // Valid user type
                  if (!$user_type) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No user type provided!']);
                        exit;
                  } else if ($user_type !== 'admin' && $user_type !== 'customer') {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid user type!']);
                        exit;
                  }

                  if (!isset($_SESSION['recovery_email'], $_SESSION['recovery_state'], $_SESSION['recovery_state_set_time']) || !$_SESSION['recovery_email'] || !$_SESSION['recovery_state'] || !$_SESSION['recovery_state_set_time']) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Server can\'t find information about client\'s recovery request!']);
                        exit;
                  } else if ($_SESSION['recovery_state']) {
                        if ($email !== $_SESSION['recovery_email']) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Email not found!']);
                              exit;
                        }

                        $current_time = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                        $interval = $current_time->getTimestamp() - $_SESSION['recovery_state_set_time']->getTimestamp();

                        if (abs($interval) > 300) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Password changing time exceeds 5 minutes time limit, please request another recovery code and try again!']);
                              exit;
                        }
                  } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Server encountered an unexpected error!']);
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
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        if ($stmt->affected_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Email not found!']);
                        } else {
                              change_password_mail($email, $user_type);
                              echo json_encode(['query_result' => true]);
                        }
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