
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
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

            $stmt = $conn->prepare('select id from customerOrder where customerID=? and status=false');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select id from customerOrder where customerID=? and status=false` preparation failed!']);
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
            $orderID = null;
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                  $orderID = $result->fetch_assoc()['id'];
            }
            $stmt->close();

            $finalResult = 0;

            if ($orderID) {
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
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  $finalResult = $result['isChanged'] ? 1 : -1;
                  $stmt->close();
            }

            $conn->close();
            echo json_encode(['query_result' => $finalResult]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>