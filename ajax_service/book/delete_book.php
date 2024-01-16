
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
      parse_str(file_get_contents('php://input'), $_DELETE);
      if (isset($_DELETE['id'])) {
            try {
                  $id = sanitize($_DELETE['id']);

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select(exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result');
                  $stmt->bind_param('ss', $id, $id);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  if ($result->num_rows !== 1) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        exit;
                  } else {
                        $result = $result->fetch_assoc();
                        if ($result['result']) {
                              echo json_encode(['error' => 'Can not delete book that has been purchased!']);
                              $stmt->close();
                              exit;
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('delete from book where id=?');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  if ($stmt->affected_rows < 0) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        echo json_encode(['query_result' => true]);
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