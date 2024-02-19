
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

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
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

            $stmt = $conn->prepare('select exists(select * from customerOrder where customerID=? and customerOrder.status=true) as result');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select exists(select * from customerOrder where customerID=? and customerOrder.status=true) as result` preparation failed!']);
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $_SESSION['id']);
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $havePurchasedOrder = $stmt->get_result()->fetch_assoc()['result'];
            $stmt->close();

            if ($havePurchasedOrder) {
                  $stmt = $conn->prepare("update customer join appUser on customer.id=appUser.id set customer.status=false,deleteTime=date_add(now(),interval 14 day) where customer.id=? and customer.status=true");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update customer join appUser on customer.id=appUser.id set customer.status=false,deleteTime=date_add(now(),interval 14 day) where customer.id=? and customer.status=true` preparation failed!']);
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
                  delete_mail($email, 1);
            }
            else
            {
                  $stmt = $conn->prepare("delete from appUser where id=?");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `delete from appUser where id=?` preparation failed!']);
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
                  delete_mail($email, 2); 
            }
            $conn->close();
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