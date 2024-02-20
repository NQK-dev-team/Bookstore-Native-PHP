
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


if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
      parse_str(file_get_contents('php://input'), $_PATCH);
      if (isset($_PATCH['rate'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $rate = sanitize(rawurldecode($_PATCH['rate']));

                  if (!$rate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Please enter conversion rate value!']);
                        exit;
                  } else if (!is_numeric($rate) || is_nan($rate) || $rate < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Conversion rate value invalid!']);
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

                  $stmt = $conn->prepare("update pointConfig set pointConversionRate=? where locker='X'");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update pointConfig set pointConversionRate=? where locker=\'X\'` preparation failed!!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('d', $rate);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();
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