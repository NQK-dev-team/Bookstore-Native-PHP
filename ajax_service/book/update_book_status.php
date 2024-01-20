
<?php
require_once __DIR__ . '/../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] === 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
      parse_str(file_get_contents('php://input'), $_PATCH);
      if (isset($_PATCH['id'])) {
            try {
                  $id = sanitize(rawurldecode($_PATCH['id']));
                  $status = filter_var(sanitize($_PATCH['status']), FILTER_VALIDATE_BOOLEAN);

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('update book set status=? where id=?');
                  $stmt->bind_param('is', $status, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        if ($stmt->affected_rows > 1) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Updated more than one book!']);
                        } else if ($stmt->affected_rows === 0) {
                              echo json_encode(['error' => 'No book found!']);
                        } else {
                              echo json_encode(['query_result' => true]);
                        }
                  }
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