
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      parse_str(file_get_contents('php://input'), $_PUT);
      if (
            isset($_PUT['email']) &&
            isset($_PUT['phone'])
      ) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = $_SESSION['update_customer_id'];
                  $email = sanitize(rawurldecode($_PUT['email']));
                  $phone = sanitize(rawurldecode($_PUT['phone']));

                  if (!$id) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Customer ID not provided!']);
                        exit;
                  }

                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  } else if (strlen($email) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email must be 255 characters long or less!']);
                        exit;
                  }

                  if (!$phone) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No phone number provided!']);
                        exit;
                  } else {
                        $matchResult = preg_match('/^[0-9]{10}$/', $phone);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during phone number format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Invalid phone number format!']);
                              exit;
                        }
                  }

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select email,phone from appUser join customer on customer.id=appUser.id where customer.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select email from appUser join customer on customer.id=appUser.id where customer.id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Customer not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $result->fetch_assoc();
                  $oldEmail = $result['email'];
                  $oldPhone = $result['phone'];
                  $stmt->close();

                  $conn->begin_transaction();

                  if ($oldEmail !== $email) {
                        $stmt = $conn->prepare('select exists(select * from appUser where email=?) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from appUser where email=?) as result` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $email);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        if ($result['result']) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Email has already been used!']);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('update appUser join customer on customer.id=appUser.id set email=? where customer.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update appUser join customer on customer.id=appUser.id set email=? where customer.id=?` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ss', $email, $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        if ($oldEmail) {
                              remove_old_email($oldEmail, $email);
                        }
                        appoint_new_email($email);
                  }

                  if ($oldPhone !== $phone) {
                        $stmt = $conn->prepare('select exists(select * from appUser where phone=?) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from appUser where phone=?) as result` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $phone);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        if ($result['result']) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Phone number has already been used!']);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('update appUser join customer on customer.id=appUser.id set phone=? where customer.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update appUser join customer on customer.id=appUser.id set phone=? where customer.id=?` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ss', $phone, $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        phone_change($email, $phone);
                  }

                  $conn->commit();
                  $conn->close();

                  echo json_encode(['query_result' => true]);
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