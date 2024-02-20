
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

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
      parse_str(file_get_contents('php://input'), $_PATCH);
      if (isset($_PATCH['id']) && isset($_PATCH['status'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_PATCH['id']));
                  $status = filter_var(sanitize($_PATCH['status']), FILTER_VALIDATE_BOOLEAN);

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select email,deleteTime,phone from customer join appUser on appUser.id=customer.id where appUser.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select email,deleteTime,phone from customer join appUser on appUser.id=customer.id where appUser.id=?` preparation failed!']);
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
                  $email = $result['email'];
                  $deleteTime = $result['deleteTime'];
                  $phone = $result['phone'];
                  $stmt->close();

                  if (!$status && !$email) {
                        http_response_code(403);
                        echo json_encode(['error' => 'This customer information has been deleted, no changes are allowed!']);
                        exit;
                  }

                  $stmt = null;
                  if ($status) {
                        if (!$email) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Customer email is not provided, can not reactivate this customer!']);
                              $conn->close();
                              exit;
                        }
                        if (!$phone) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Customer phone number is not provided, can not reactivate this customer!']);
                              $conn->close();
                              exit;
                        }
                        $stmt = $conn->prepare('update customer set status=?,deleteTime=null where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update customer set status=?,deleteTime=null where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                  } else {
                        $stmt = $conn->prepare('update customer set status=? where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update customer set status=? where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                  }
                  $stmt->bind_param('is', $status, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        if ($stmt->affected_rows > 1) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Updated more than one customer!']);
                        }
                  }
                  $stmt->close();

                  if (!$status && $email) {
                        deactivate_mail($email);
                  } else if ($status && $email) {
                        if ($deleteTime) {
                              delete_cancel_mail($email);
                              echo json_encode(['query_result' => 100]);
                              exit;
                        } else
                              activate_mail($email);
                  }

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