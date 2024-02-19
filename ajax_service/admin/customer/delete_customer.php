
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/delete_directory.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/send_mail.php';
require_once __DIR__ . '/../../../tool/php/delete_directory.php';


if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
      parse_str(file_get_contents('php://input'), $_DELETE);
      if (isset($_DELETE['id'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_DELETE['id']));

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select email,status,deleteTime,imagePath from customer join appUser on appUser.id=customer.id where customer.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select email,status,deleteTime,imagePath from customer join appUser on appUser.id=customer.id where customer.id=?` preparation failed!']);
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
                  $status = $result['status'];
                  $deleteTime = $result['deleteTime'];
                  $imagePath = $result['imagePath'];
                  $stmt->close();

                  if (!$status && !$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'This customer information has already been deleted!']);
                        $conn->close();
                        exit;
                  }

                  if (!$status && $deleteTime) {
                        http_response_code(400);
                        echo json_encode(['error' => 'This customer information has already been in the deletion process!']);
                        $conn->close();
                        exit;
                  }

                  $stmt = $conn->prepare('select exists(select * from customerOrder where customerID=? and customerOrder.status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from customerOrder where customerID=? and customerOrder.status=true) as result` preparation failed!']);
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
                  $result = $stmt->get_result()->fetch_assoc()['result'];
                  $stmt->close();

                  if ($result) {
                        $stmt = $conn->prepare('update customer set status=false,deleteTime=date_add(now(),interval 14 day) where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update customer set status=false,deleteTime=date_add(now(),interval 14 day) where id=?` preparation failed!']);
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
                        $stmt->close();

                        delete_mail($email, 1);
                        echo json_encode(['query_result' => 1]);
                  } else {
                        $stmt = $conn->prepare('delete from appUser where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `delete from appUser where id=?` preparation failed!']);
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
                        $stmt->close();

                        // Remove image directory
                        if ($imagePath) {
                              rrmdir(dirname(__DIR__ . '/../../../data/user/customer/' . $imagePath));
                        }

                        delete_mail($email, 2);
                        echo json_encode(['query_result' => 2]);
                  }

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