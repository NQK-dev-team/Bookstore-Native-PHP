
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
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['id'])) {
    try {
      if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF token validation failed!']);
        exit;
      }

      $bookID = sanitize(rawurldecode($_POST['id']));

      $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

      // Check connection
      if (!$conn) {
        http_response_code(500);
        echo json_encode(['error' => 'MySQL Connection Failed!']);
        exit;
      }

      $stmt = $conn->prepare("SELECT * FROM fileCopy WHERE id=?");
      if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Query `SELECT * FROM fileCopy WHERE id=?` preparation failed!']);
        exit;
      }
      $stmt->bind_param("s", $bookID);
      if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
      }
      $result = $stmt->get_result();
      if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found!']);
        $stmt->close();
        $conn->close();
        exit;
      }
      $stmt->close();

      $conn->begin_transaction();

      $stmt = $conn->prepare("call addFileToCart(?,?)");
      if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Query `call addFileToCart(?,?)` preparation failed!']);
        $conn->rollback();
        $conn->close();
        exit;
      }
      $stmt->bind_param("ss", $_SESSION['id'], $bookID);
      if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => $stmt->error]);
        $stmt->close();
        $conn->rollback();
        $conn->close();
        exit;
      }
      $stmt->close();

      $conn->commit();

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