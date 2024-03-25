
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session()) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
} else if ($_SESSION['type'] !== 'admin') {
      http_response_code(400);
      echo json_encode(['error' => 'Bad request!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/password.php';
require_once __DIR__ . '/../../../tool/php/send_mail.php';


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      parse_str(file_get_contents('php://input'), $_PUT);
      if (
            isset($_PUT['oldPassword']) &&
            isset($_PUT['newPassword']) &&
            isset($_PUT['confirmPassword'])
      ) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $oldPassword = sanitize(rawurldecode($_PUT['oldPassword']));
                  $newPassword = sanitize(rawurldecode($_PUT['newPassword']));
                  $confirmPassword = sanitize(rawurldecode($_PUT['confirmPassword']));

                  if (!$oldPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Current password not provided!']);
                        exit;
                  }

                  if (!$newPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'New password not provided!']);
                        exit;
                  } else if (strlen($newPassword) < 8) {
                        http_response_code(400);
                        echo json_encode(['error' => 'New password must be at least 8 characters long!']);
                        exit;
                  } else {
                        $matchResult = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,72}$/', $newPassword);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during password format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters!']);
                              exit;
                        }
                  }

                  if (!$confirmPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No confirm new password provided!']);
                        exit;
                  } else if ($confirmPassword !== $newPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Confirm new password does not match!']);
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

                  $stmt = $conn->prepare('select password,email from appUser join admin on admin.id=appUser.id where admin.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select password,email from appUser join admin on admin.id=appUser.id where admin.id=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $_SESSION['id']);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  $password = $result['password'];
                  $email = $result['email'];
                  if (!verify_password($oldPassword, $password)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Current password not correct!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $hashedPassword = hash_password($newPassword);
                  if ($hashedPassword === false) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Password hashing failed!']);
                        $conn->close();
                        exit;
                  } else if (is_null($hashedPassword)) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Password hashing algorithm invalid!']);
                        $conn->close();
                        exit;
                  }

                  $stmt = $conn->prepare('UPDATE appUser join admin on admin.id=appUser.id SET password=? WHERE admin.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `UPDATE appUser join admin on admin.id=appUser.id SET password=? WHERE admin.id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $hashedPassword, $_SESSION['id']);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();
                  change_password_mail($email, 'admin');
                  echo json_encode(['query_result' => true]);
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