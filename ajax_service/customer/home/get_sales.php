
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/converter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  echo json_encode(['error' => 'MySQL Connection Failed!']);
                  exit;
            }

            $stmt = $conn->prepare('call getDiscountBooks()');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `call getDiscountBooks()` preparation failed!']);
                  $conn->close();
                  exit;
            }
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
            }

            $eventDetail = [];
            $queryResult = [];

            if ($stmt->more_results()) {
                  $result = $stmt->get_result();
                  if ($result->num_rows === 1) {
                        $row = $result->fetch_assoc();
                        $row['startDate'] = $row['startDate'] . ' 00:00:00';
                        $row['endDate'] = $row['endDate'] . ' 23:59:59';
                        $eventDetail = $row;
                  }

                  $result->free();
                  $stmt->next_result();
                  $result = $stmt->get_result();

                  while ($row = $result->fetch_assoc()) {
                        $queryResult[] = $row;
                  }
                  $result->free();
                  $stmt->close();

                  foreach ($queryResult as $idx => &$row) {
                        $host = $_SERVER['HTTP_HOST'];
                        $row['imagePath'] = "https://$host/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
                        $row['edition'] = convertToOrdinal($row['edition']);

                        $id = $row['id'];

                        $sub_stmt = $conn->prepare('select authorName from author where bookID=? order by authorName,authorIdx');
                        if (!$sub_stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select authorName from author where bookID=? order by authorName,authorIdx` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $sub_stmt->bind_param('s', $id);
                        $isSuccess = $sub_stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $sub_stmt->error]);
                              $sub_stmt->close();
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $sub_result = $sub_stmt->get_result();
                        if ($sub_result->num_rows === 0) {
                              $row['author'] = [];
                        } else {
                              while ($sub_row = $sub_result->fetch_assoc()) {
                                    $row['author'][] = $sub_row['authorName'];
                              }
                        }
                        $sub_stmt->close();

                        $sub_stmt = $conn->prepare('select price from physicalCopy where id=?');
                        if (!$sub_stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select price from physicalCopy where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $sub_stmt->bind_param('s', $id);
                        if (!$sub_stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $sub_stmt->error]);
                              $sub_stmt->close();
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $sub_result = $sub_stmt->get_result();
                        if ($sub_result->num_rows === 0) {
                              $row['physicalPrice'] = null;
                        } else {
                              $sub_row = $sub_result->fetch_assoc();
                              $row['physicalPrice'] = $sub_row['price'];
                        }
                        $sub_stmt->close();

                        $sub_stmt = $conn->prepare('select price from fileCopy where id=?');
                        if (!$sub_stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select price from fileCopy where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $sub_stmt->bind_param('s', $id);
                        if (!$sub_stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $sub_stmt->error]);
                              $sub_stmt->close();
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $sub_result = $sub_stmt->get_result();
                        if ($sub_result->num_rows === 0) {
                              $row['filePrice'] = null;
                        } else {
                              $sub_row = $sub_result->fetch_assoc();
                              $row['filePrice'] = $sub_row['price'];
                        }
                        $sub_stmt->close();
                  }
            }

            $conn->close();

            if (count($eventDetail) === 0 || count($queryResult) === 0)
                  echo json_encode(['query_result' => []]);
            else
                  echo json_encode(['query_result' => [$eventDetail, $queryResult]]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>