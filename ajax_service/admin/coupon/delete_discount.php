
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


if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
      parse_str(file_get_contents('php://input'), $_DELETE);
      if (isset($_DELETE['id']) && isset($_DELETE['type'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_DELETE['id']));
                  $type = sanitize(rawurldecode($_DELETE['type']));

                  if (!is_numeric($type) || is_nan($type) || ($type !== '1' && $type !== '2' && $type !== '3')) {
                        http_response_code(400);
                        echo json_encode(['error' => '`Coupon Type` data type invalid!']);
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

                  if ($type === '1') {
                        $stmt = $conn->prepare('select * from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              exit;
                        } else if ($stmt->get_result()->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result` preparation failed!']);
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
                        } else if ($stmt->get_result()->fetch_assoc()['result']) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Can not delete discount coupon that has been apply on purchased order(s)!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('delete from discount where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `delete from discount where id=?` preparation failed!']);
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
                  } else if ($type === '2') {
                        $stmt = $conn->prepare('select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              exit;
                        } else if ($stmt->get_result()->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?)  as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?)  as result` preparation failed!']);
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
                        } else if ($stmt->get_result()->fetch_assoc()['result']) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Can not delete discount coupon that has been apply on purchased order(s)!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('delete from discount where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `delete from discount where id=?` preparation failed!']);
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
                  } else if ($type === '3') {
                        $stmt = $conn->prepare('select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              exit;
                        } else if ($stmt->get_result()->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result` preparation failed!']);
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
                        } else if ($stmt->get_result()->fetch_assoc()['result']) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Can not delete discount coupon that has been apply on purchased order(s)!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('delete from discount where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `delete from discount where id=?` preparation failed!']);
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