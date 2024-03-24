
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
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      parse_str(file_get_contents('php://input'), $_PUT);
      if (isset($_PUT['id'], $_PUT['amount'])) {
            try {
                  $id = sanitize(rawurldecode($_PUT['id']));
                  $amount = sanitize(rawurldecode($_PUT['amount']));

                  if (!is_numeric($amount) || is_nan($amount) || $amount <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book amount invalid!']);
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

                  $stmt = $conn->prepare('select inStock from physicalCopy where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select inStock from physicalCopy where id=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Book not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $result->fetch_assoc();
                  $inStock = $result['inStock'];
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from physicalOrderContain where bookID=? and orderID=?) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from physicalOrderContain where bookID=? and orderID=?) as result` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $orderID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  if (!$result['result']) {
                        http_response_code(400);
                        echo json_encode(['error' => 'This book\'s physical copy is not in your cart!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $conn->begin_transaction();

                  if ($amount > $inStock) {
                        echo json_encode(['error' => 'Not enough book in stock!']);
                        $conn->close();
                        exit;
                  }

                  $stmt = $conn->prepare('update physicalOrderContain set amount=? where orderID=? and bookID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update physicalOrderContain set amount=? where orderID=? and bookID=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('iss', $amount, $orderID, $id);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('call reEvaluateOrder(?,@nullVar);');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `call reEvaluateOrder(?,@nullVar);` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $orderID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
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