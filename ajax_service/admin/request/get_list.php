
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (
            isset($_GET['entry']) &&
            isset($_GET['offset'])
      ) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of entries of books!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of entries of books invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing book list number']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book list number invalid!']);
                        exit;
                  }

                  $offset = ($offset - 1) * $entry;

                  $queryResult = [];
                  $totalEntries = 0;

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare("SELECT COUNT(*) as result FROM request");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `SELECT COUNT(*) as result FROM request` preparation failed!']);
                        exit;
                  }
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        exit;
                  }
                  $stmt->bind_result($totalEntries);
                  $stmt->fetch();
                  $stmt->close();

                  $stmt = $conn->prepare("SELECT * FROM request ORDER BY requestTime DESC LIMIT ? OFFSET ?");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `SELECT * FROM request ORDER BY requestTime DESC LIMIT ? OFFSET ?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('ii', $entry, $offset);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        exit;
                  }
                  $result = $stmt->get_result();
                  while ($row = $result->fetch_assoc()) {
                        $row['requestTime'] = formatOrderTime($row['requestTime']);
                        $queryResult[] = $row;
                  }
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