
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
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/send_mail.php';


if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
      try {
            if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                  http_response_code(403);
                  echo json_encode(['error' => 'CSRF token validation failed!']);
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

            $stmt = $conn->prepare('select email from appUser join customer on customer.id=appUser.id where appUser.id=? and status=true');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select email from appUser join customer on customer.id=appUser.id where appUser.id=? and status=true` preparation failed!']);
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $_SESSION['id']);
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
                  exit;
            } else {
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  $email = $result['email'];
            }
            $stmt->close();

            $conn->begin_transaction();
            $stmt = $conn->prepare("update customer join appUser on customer.id=appUser.id set customer.status=false where customer.id=? and customer.status=true");
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `update customer join appUser on customer.id=appUser.id set customer.status=false where customer.id=? and customer.status=true` preparation failed!']);
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $_SESSION['id']);
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $stmt->close();
            $conn->commit();
            $conn->close();
            deactivate_mail($email);
            echo json_encode(['query_result' => true]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>