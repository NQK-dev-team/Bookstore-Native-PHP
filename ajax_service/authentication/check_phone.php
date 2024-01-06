
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['phone'])) {
            $phone = sanitize($_GET['phone']);

            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  echo json_encode(['error' => 'MySQL Connection Failed!']);
                  exit;
            }

            // Using prepare statement (preventing SQL injection)
            $stmt = $conn->prepare("select * from appUser where phone=?");
            $stmt->bind_param('s', $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
                  echo json_encode(['query_result' => true]);
            else {
                  echo json_encode(['query_result' => false]);
            }

            // Close connection
            $conn->close();
      } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>