
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['code']) && isset($_GET['date'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = $_SESSION['update_customer_id'];
                  $code = sanitize(rawurldecode(str_replace('-', '', $_GET['code'])));
                  $date = sanitize(rawurldecode($_GET['date']));

                  $code = '%' . $code . '%';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select point from customer where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select point from customer where id=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Customer not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $point = $result->fetch_assoc()['point'];
                  $stmt->close();

                  $orders = [];

                  if ($date) {
                        $stmt = $conn->prepare('select id as orderID,purchaseTime,totalCost,totalDiscount,orderCode from customerOrder where customerID=? and date(purchaseTime)=? and orderCode like ? and status=true order by purchaseTime desc');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select id,purchaseTime,totalCost,totalDiscount,orderCode from customerOrder where customerID=? and date(purchaseTime)=? and orderCode like ? and status=true` preparation failed!']);
                              exit;
                        }
                        $stmt->bind_param('sss', $id, $date, $code);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                              $row['purchaseTime'] = formatOrderTime($row['purchaseTime']);
                              $sub_stmt = $conn->prepare('select distinct combined.name,combined.edition from (
                              select book.name,book.edition from book join fileOrderContain on fileOrderContain.bookID=book.id where fileOrderContain.orderID=?
                              union
                              select book.name,book.edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where physicalOrderContain.orderID=? 
                              ) as combined');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select distinct combined.bookID,combined.name,combined.edition from (select book.id as bookID,book.name,book.edition from book join fileOrderContain on fileOrderContain.bookID=book.id where fileOrderContain.orderID=? union select book.id as bookID,book.name,book.edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where physicalOrderContain.orderID=?) as combined` preparation failed!']);
                                    exit;
                              }
                              $sub_stmt->bind_param('ss', $row['orderID'], $row['orderID']);
                              if (!$sub_stmt->execute()) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              while ($sub_row = $sub_result->fetch_assoc())
                                    $row['books'][] = $sub_row;
                              $orders[] = $row;
                        }
                  } else {
                        $stmt = $conn->prepare('select id as orderID,purchaseTime,totalCost,totalDiscount,orderCode from customerOrder where customerID=? and orderCode like ? and status=true order by purchaseTime desc');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select id,purchaseTime,totalCost,totalDiscount,orderCode from customerOrder where customerID=? and orderCode like ? and status=true` preparation failed!']);
                              exit;
                        }
                        $stmt->bind_param('ss', $id, $code);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                              $row['purchaseTime'] = formatOrderTime($row['purchaseTime']);
                              $row['orderCode'] = splitOrderCode($row['orderCode']);
                              $sub_stmt = $conn->prepare('select distinct combined.bookID,combined.name,combined.edition from (
                              select book.id as bookID,book.name,book.edition from book join fileOrderContain on fileOrderContain.bookID=book.id where fileOrderContain.orderID=?
                              union
                              select book.id as bookID,book.name,book.edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where physicalOrderContain.orderID=? 
                              ) as combined');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select distinct combined.bookID,combined.name,combined.edition from (select book.id as bookID,book.name,book.edition from book join fileOrderContain on fileOrderContain.bookID=book.id where fileOrderContain.orderID=? union select book.id as bookID,book.name,book.edition from book join physicalOrderContain on physicalOrderContain.bookID=book.id where physicalOrderContain.orderID=?) as combined` preparation failed!']);
                                    exit;
                              }
                              $sub_stmt->bind_param('ss', $row['orderID'], $row['orderID']);
                              if (!$sub_stmt->execute()) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              while ($sub_row = $sub_result->fetch_assoc()) {
                                    $sub_row['edition'] = convertToOrdinal($sub_row['edition']);
                                    $row['books'][] = $sub_row;
                              }
                              $orders[] = $row;
                        }
                  }

                  $conn->close();
                  echo json_encode(['query_result' => [$point, $orders]]);
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