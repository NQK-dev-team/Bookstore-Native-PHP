
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session()) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
} else if ($_SESSION['type'] !== 'customer') {
      http_response_code(400);
      echo json_encode(['error' => 'Bad request!']);
      exit;
}

require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
      parse_str(file_get_contents('php://input'), $_DELETE);
      if (isset($_DELETE['id'], $_DELETE['mode'])) {
            try {
                  $id = sanitize(rawurldecode($_DELETE['id']));
                  $mode = sanitize(rawurldecode($_DELETE['mode']));

                  if ($mode != 1 && $mode != 2) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Delete mode invalid!']);
                        exit;
                  }

                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select id from customerOrder where status=false and customerID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select id from customerOrder where status=false and customerID=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $_SESSION['id']);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'You have no unpaid order!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $result->fetch_assoc();
                  $orderID = $result['id'];
                  $stmt->close();

                  $conn->begin_transaction();

                  $stmt = $conn->prepare('call removeBookFromCart(?,?,?)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `call removeBookFromCart(?,?,?)` preparation failed!']);
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ssi', $orderID, $id, $mode);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

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