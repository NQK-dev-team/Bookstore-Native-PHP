
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';
require_once __DIR__ . '/../../../tool/php/check_https.php';

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

            $finalResult = [];

            $stmt = $conn->prepare("select imagePath,name,edition,book.id,fileCopy.price
                  from book join fileOrderContain on fileOrderContain.bookID=book.id
                  join fileCopy on fileCopy.id=book.id
                  join customerOrder on customerOrder.id=fileOrderContain.orderID
                  where customerOrder.customerID=? and customerOrder.status=false order by name,book.id");

            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select imagePath,name,edition,book.id,fileCopy.price
                  from book join fileOrderContain on fileOrderContain.bookID=book.id
                  join fileCopy on fileCopy.id=book.id
                  join customerOrder on customerOrder.id=fileOrderContain.orderID
                  where customerOrder.customerID=? and customerOrder.status=false order by name,book.id` preparation failed!']);
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
            while ($row = $result->fetch_assoc()) {
                  $row['edition'] = convertToOrdinal($row['edition']);
                  $row['imagePath'] = (isSecure() ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));

                  $sub_stmt = $conn->prepare("select combined.discount from (
						select distinct discount.id,eventDiscount.discount,1 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=?
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id limit 1");
                  if (!$sub_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select combined.discount from (
						select distinct discount.id,eventDiscount.discount,1 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=?
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id limit 1` preparation failed!']);
                        exit;
                  }
                  $sub_stmt->bind_param('s', $row['id']);
                  if (!$sub_stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $sub_stmt->error]);
                        $sub_stmt->close();
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $sub_result = $sub_stmt->get_result();
                  if ($sub_result->num_rows === 1) {
                        $sub_result = $sub_result->fetch_assoc();
                        $row['discount'] = $sub_result['discount'];
                  } else if ($sub_result->num_rows === 0) {
                        $row['discount'] = null;
                  }
                  $sub_stmt->close();

                  $finalResult['detail'][] = $row;
            }
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