
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session()) {
      echo json_encode(['query_result' => false]);
      exit;
} else if ($_SESSION['type'] !== 'customer') {
      http_response_code(400);
      echo json_encode(['error' => 'Bad request!']);
      exit;
}

require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['id'])) {
            try {
                  $id = sanitize(rawurldecode($_GET['id']));

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select exists(
                        select * from fileOrderContain
                        join customerOrder on customerOrder.id=fileOrderContain.orderID
                        join book on fileOrderContain.bookID=book.id and book.status=true
                        where customerOrder.status=false and fileOrderContain.bookID=? and customerOrder.customerID=?
                        ) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(
                        select * from fileOrderContain
                        join customerOrder on customerOrder.id=fileOrderContain.orderID
                        join book on fileOrderContain.bookID=book.id and book.status=true
                        where customerOrder.status=false and fileOrderContain.bookID=? and customerOrder.customerID=?
                        ) as result` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $_SESSION['id']);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        exit;
                  }
                  $result = $stmt->get_result()->fetch_assoc()['result'];
                  $stmt->close();

                  $conn->close();
                  echo json_encode(['query_result' => $result]);
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