
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['entry']) && isset($_GET['offset']) && isset($_GET['search'])) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $search = sanitize(rawurldecode($_GET['search']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing `Number Of Entries`!']);
                        exit;
                  }else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => '`Number Of Entries` data type invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing `List Number`!']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => '`List Number` data type invalid!']);
                        exit;
                  }

                  $queryResult = [];
                  $totalEntries = 0;

                  $search = '%' . $search . '%';
                  $offset = ($offset - 1) * $entry;

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select * from category where name like ? order by name,id limit ? offset ?');
                  $stmt->bind_param('sii', $search, $entry, $offset);
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        exit;
                  }
                  $result = $stmt->get_result();
                  while ($row = $result->fetch_assoc()) {
                        $queryResult[] = $row;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select count(*) as total from category where name like ?');
                  $stmt->bind_param('s', $search);
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        exit;
                  }
                  $result = $stmt->get_result();
                  $totalEntries = $result->fetch_assoc()['total'];
                  $stmt->close();

                  echo json_encode(['query_result' => [$queryResult, $totalEntries]]);

                  // Close connection
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