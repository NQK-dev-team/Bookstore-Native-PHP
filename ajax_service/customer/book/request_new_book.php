
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['name'], $_POST['author'])) {
            try {
                  $name = sanitize(rawurldecode($_POST['name']));
                  $author = sanitize(rawurldecode($_POST['author']));

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Please enter book name!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book name is no long than 255 characters!']);
                        exit;
                  }

                  if (!$author) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Please enter author name!']);
                        exit;
                  }

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('insert into request (name,author) values (?,?);');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `insert into request (name,author) values (?,?);` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $name, $author);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

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