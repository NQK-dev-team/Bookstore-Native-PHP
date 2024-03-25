
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
            if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                  http_response_code(403);
                  echo json_encode(['error' => 'CSRF token validation failed!']);
                  exit;
            }

            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  echo json_encode(['error' => 'MySQL Connection Failed!']);
                  exit;
            }

            $finalResult = ['originalCost' => 0, 'costAfterCoupon' => 0, 'loyalty' => 0, 'referrer' => 0, 'final' => 0, 'discount' => 0];

            $stmt = $conn->prepare('select id from customerOrder where customerID=? and status=false');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `elect id from customerOrder where customerID=? and status=false` preparation failed!']);
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
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                  $stmt->close();
                  $conn->close();
                  echo json_encode(['query_result' => $finalResult]);
                  exit;
            }
            $row = $result->fetch_assoc();
            $orderID = $row['id'];
            $stmt->close();

            $stmt = $conn->prepare('call getBillingDetail(?)');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `call getBillingDetail(?)` preparation failed!']);
                  exit;
            }
            $stmt->bind_param('s', $orderID);
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $finalResult['originalCost'] = $row['originalCost'];
            $finalResult['costAfterCoupon'] = $row['costAfterCoupon'];
            $finalResult['loyalty'] = $row['loyaltyDiscount'];
            $finalResult['referrer'] = $row['referrerDiscount'];
            $finalResult['final'] = $row['finalCost'];
            $finalResult['discount'] = $row['finalDiscount'];
            $stmt->close();

            $conn->close();
            echo json_encode(['query_result' => $finalResult]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>