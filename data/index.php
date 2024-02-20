
<?php
require_once __DIR__ . '/../tool/php/session_check.php';
require_once __DIR__ . '/../tool/php/converter.php';
require_once __DIR__ . '/../tool/php/sanitizer.php';
require_once __DIR__ . '/../config/db_connection.php';

$requestedUri = sanitize(rawurldecode($_SERVER['REQUEST_URI']));
$filePath = dirname(__DIR__) . $requestedUri;

$realPath = realpath($filePath);
if ($realPath === false || strpos($realPath, __DIR__) !== 0) {
      echo json_encode(['error' => 'Invalid file path!']);
      http_response_code(400);
      exit;
}

$contentType = mime_content_type($filePath);
if ($contentType === false) {
      echo json_encode(['error' => 'File not found or something is wrong!']);
      http_response_code(500);
      exit;
}

# If contentType is not pdf or image, then it is not allowed to be accessed
if ($contentType !== 'application/pdf' && $contentType !== 'image/png' && $contentType !== 'image/jpeg') {
      echo json_encode(['error' => 'File type invalid!']);
      http_response_code(400);
      exit;
}

if (check_session() && $_SESSION['type'] === 'admin') {
      // Set the appropriate Content-Type header
      header('Content-Type: ' . $contentType);
      header('Content-Length: ' . filesize($filePath));

      // Fetch and output the content of the requested URI
      ob_clean();
      readfile($filePath);
      flush();
      exit;
} else {
      if ($contentType === 'image/png' || $contentType === 'image/jpeg') {
            // Set the appropriate Content-Type header
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . filesize($filePath));

            // Fetch and output the content of the requested URI
            ob_clean();
            readfile($filePath);
            flush();
            exit;
      } else if ($contentType === 'application/pdf') {
            if (check_session() && $_SESSION['type'] === 'customer') {
                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $bookID = explode('/', $requestedUri);
                  $bookID = $bookID[count($bookID) - 2];

                  $stmt = $conn->prepare('select exists(select * from fileOrderContain join customerOrder on fileOrderContain.orderID=customerOrder.id where fileOrderContain.bookID=? and customerOrder.status=true and customerOrder.customerID=?) as result');
                  if(!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from fileOrderContain join customerOrder on fileOrderContain.orderID=customerOrder.id where fileOrderContain.bookID=? and customerOrder.status=true and customerOrder.customerID=?) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $bookID, $_SESSION['id']);
                  if(!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result=$stmt->get_result()->fetch_assoc()['result'];
                  if(!$result)
                  {
                        http_response_code(403);
                        echo json_encode(['error' => 'No purchase has been made for this book, access denied!']);
                        $conn->close();
                        exit;
                  }
                  $conn->close();

                  // Set the appropriate Content-Type header
                  header('Content-Type: ' . $contentType);
                  header('Content-Length: ' . filesize($filePath));

                  // Fetch and output the content of the requested URI
                  ob_clean();
                  readfile($filePath);
                  flush();
                  exit;
            } else {
                  echo json_encode(['error' => 'Not authenticated!']);
                  http_response_code(401);
                  exit;
            }
      } else {
            echo json_encode(['error' => 'File type invalid!']);
            http_response_code(400);
            exit;
      }
}
?>