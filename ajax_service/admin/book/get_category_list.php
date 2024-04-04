
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../tool/php/converter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['search'])) {
            try {      
                  $search = sanitize(rawurldecode($_GET['search']));

                  $search = '%' . $search . '%';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $query_result = [];

                  $stmt = $conn->prepare('select name,description from category where name like ? order by name,id');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select name,description from category where name like ? order by name,id` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $search);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                                    $query_result[] = $row;
                              }
                        }
                  }
                  echo json_encode(['query_result' => $query_result]);
                  $stmt->close();
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