
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
require_once __DIR__ . '/../../../tool/php/notify_event.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
            if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                  http_response_code(403);
                  echo json_encode(['error' => 'CSRF token validation failed!']);
                  exit;
            }

            if (!isset($_POST['type'])) {
                  http_response_code(400);
                  echo json_encode(['error' => 'Missing coupon type parameter!']);
                  exit;
            }

            $type = sanitize(rawurldecode($_POST['type']));

            if (!is_numeric($type) || is_nan($type) || ($type !== '1' && $type !== '2' && $type !== '3')) {
                  http_response_code(400);
                  echo json_encode(['error' => '`Coupon Type` data type invalid!']);
                  exit;
            }

            if ($type === '1') {
            } else if ($type === '2') {
            } else if ($type === '3') {
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
                  
            } else if ($type === '2') {
                  if(!isset($_POST['discount']) || !isset($_POST['point'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Mising discount percentage value or accumulated point value!']);
                        exit;
                  }
            } else if ($type === '3') {
                  if (!isset($_POST['discount']) || !isset($_POST['people'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Mising discount percentage value or number of people value!']);
                        exit;
                  }
            }

            $conn->close();
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>