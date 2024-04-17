
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['id'])) {
            try {
                  $id = sanitize(rawurldecode($_GET['id']));

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  $finalResult = 0;

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }
                  $stmt = $conn->prepare("SELECT inStock from physicalCopy where id=?");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `SELECT inStock from physicalCopy where id=?` preparation failed!']);
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
                  if ($result->num_rows === 1) {
                        $finalResult = $result->fetch_assoc()['inStock'];
                  } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $conn->close();
                  echo json_encode(['query_result' => $finalResult]);
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