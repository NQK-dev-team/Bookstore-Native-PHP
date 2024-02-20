
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';


function map($elem)
{
      return sanitize(rawurldecode($elem));
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      parse_str(file_get_contents('php://input'), $_PUT);
      if (isset($_PUT['name']) && isset($_PUT['description']) && isset($_PUT['id'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_PUT['id']));
                  $name = sanitize(rawurldecode($_PUT['name']));
                  $description = $_PUT['description'] ? sanitize(rawurldecode($_PUT['description'])) : null;

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Category name is empty!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Category name must be at most 255 characters long or less!']);
                        exit;
                  }

                  if ($description && strlen($description) > 500) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Category description must be at most 500 characters long or less!']);
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

                  $stmt = $conn->prepare('select * from category where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from category where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Category ID not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select * from category where name=? and id!=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from category where name=? and id!=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $name, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  } else if ($stmt->get_result()->num_rows !== 0) {
                        echo json_encode(['error' => 'Category already exists!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('update category set name=?,description=? where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update category set name=?,description=? where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sss', $name, $description, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  echo json_encode(['query_result' => true]);

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