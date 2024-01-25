
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

            

            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  echo json_encode(['error' => 'MySQL Connection Failed!']);
                  exit;
            }

            $conn->close();
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
      // http_response_code(400);
      // echo json_encode(['error' => 'Invalid data received!']);
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>