
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
require_once __DIR__ . '/../../../tool/php/send_mail.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['deliveryAddress'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $deliveryAddress = sanitize(rawurldecode($_POST['deliveryAddress']));

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select id from customerOrder where status=false and customerID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select id from customerOrder where status=false and customerID=?` preparation failed!']);
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
                        echo json_encode(['error' => 'You have no unpaid order!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $result->fetch_assoc();
                  $orderID = $result['id'];
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from physicalOrderContain where orderID=?) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from physicalOrderContain where orderID=?) as result` preparation failed!']);
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
                  $doContainPhysicalCopy = $stmt->get_result()->fetch_assoc()['result'];

                  if (!$deliveryAddress && $doContainPhysicalCopy) {
                        echo json_encode(['error' => 'No delivery address provided!']);
                        exit;
                  }
                  $stmt->close();

                  $conn->begin_transaction();

                  $stmt = $conn->prepare('call reEvaluateOrder(?,@nullVar)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `call reEvaluateOrder(?,@nullVar)` preparation failed!']);
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
                  $result = $stmt->get_result()->fetch_assoc()['isChanged'];
                  if ($result) {
                        echo json_encode(['error' => 'Your billing information has changed, please try again!']);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select bookID,book.name,book.edition from physicalOrderContain join book on book.id=bookID where orderID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select bookID,book.name,book.edition from physicalOrderContain join book on book.id=bookID where orderID=?` preparation failed!']);
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }

                  $get_amount_stmt = $conn->prepare('select amount from physicalOrderContain where orderID=? and bookID=?');
                  if (!$get_amount_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select amount from physicalOrderContain where orderID=? and bookID=?` preparation failed!']);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }

                  $get_inStock_stmt = $conn->prepare('select inStock from physicalCopy where id=?');
                  if (!$get_inStock_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select inStock from physicalCopy where id=?` preparation failed!']);
                        $stmt->close();
                        $get_amount_stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }

                  $update_inStock_stmt = $conn->prepare('update physicalCopy set inStock=inStock-? where id=?');
                  if (!$update_inStock_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update physicalCopy set inStock=inStock-? where id=?` preparation failed!']);
                        $stmt->close();
                        $get_amount_stmt->close();
                        $get_inStock_stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }

                  $stmt->bind_param('s', $orderID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $get_amount_stmt->close();
                        $get_inStock_stmt->close();
                        $update_inStock_stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  while ($row = $result->fetch_assoc()) {
                        $bookID = $row['bookID'];
                        $name = $row['name'];
                        $edition = convertToOrdinal($row['edition']);

                        $get_amount_stmt->bind_param('ss', $orderID, $bookID);
                        if (!$get_amount_stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $get_amount_stmt->error]);
                              $stmt->close();
                              $get_amount_stmt->close();
                              $get_inStock_stmt->close();
                              $update_inStock_stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $amount = $get_amount_stmt->get_result()->fetch_assoc()['amount'];
                        $get_amount_stmt->free_result();

                        $get_inStock_stmt->bind_param('s', $bookID);
                        if (!$get_inStock_stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $get_inStock_stmt->error]);
                              $stmt->close();
                              $get_amount_stmt->close();
                              $get_inStock_stmt->close();
                              $update_inStock_stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $inStock = $get_inStock_stmt->get_result()->fetch_assoc()['inStock'];
                        $get_inStock_stmt->free_result();

                        if ($inStock < $amount) {
                              echo json_encode(['error' => 'Not enough stock for ' . $name . ' - ' . $edition]);
                              $stmt->close();
                              $get_amount_stmt->close();
                              $get_inStock_stmt->close();
                              $update_inStock_stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }

                        $update_inStock_stmt->bind_param('is', $amount, $bookID);
                        if (!$update_inStock_stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $update_inStock_stmt->error]);
                              $stmt->close();
                              $get_amount_stmt->close();
                              $get_inStock_stmt->close();
                              $update_inStock_stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                  }
                  $stmt->close();
                  $get_amount_stmt->close();
                  $get_inStock_stmt->close();
                  $update_inStock_stmt->close();

                  if ($doContainPhysicalCopy) {
                        $stmt = $conn->prepare('update physicalOrder set destinationAddress=? where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update physicalOrder set destinationAddress=? where id=?` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ss', $deliveryAddress, $orderID);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $stmt = $conn->prepare('call purchaseOrder(?)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `call purchaseOrder(?)` preparation failed!']);
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $orderID);
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

                  $stmt = $conn->prepare('select email from appUser where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select email from appUser where id=?` preparation failed!']);
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
                  $email = $stmt->get_result()->fetch_assoc()['email'];
                  $stmt->close();

                  $stmt = $conn->prepare('select orderCode,purchaseTime,totalCost,totalDiscount from customerOrder where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select orderCode,purchaseTime,totalCost,totalDiscount from customerOrder where id=?` preparation failed!']);
                        $conn->close();
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
                  $result = $stmt->get_result()->fetch_assoc();
                  $orderCode = splitOrderCode($result['orderCode']);
                  $purchaseTime = $result['purchaseTime'];
                  $totalCost = $result['totalCost'];
                  $totalDiscount = $result['totalDiscount'];
                  $stmt->close();

                  $stmt = $conn->prepare('select name,edition from book join fileOrderContain on fileOrderContain.bookID=book.id where orderID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select name,edition from book join fileOrderContain on fileOrderContain.bookID=book.id where orderID=?` preparation failed!']);
                        $conn->close();
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
                  $fileOrder = [];
                  while ($row = $result->fetch_assoc()) {
                        $fileOrder[] = $row['name'] . ' - ' . convertToOrdinal($row['edition']) . ' edition';
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select name,edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where orderID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select name,edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where orderID=?` preparation failed!']);
                        $conn->close();
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
                  $physicalOrder = [];
                  while ($row = $result->fetch_assoc()) {
                        $physicalOrder[] = $row['name'] . ' - ' . convertToOrdinal($row['edition']) . ' edition';
                  }
                  $stmt->close();

                  billing_mail($email, $orderCode, $purchaseTime, $totalDiscount, $totalCost, $fileOrder, $physicalOrder, $deliveryAddress);

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