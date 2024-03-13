
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['id'])) {
            try {
                  $id = sanitize(rawurldecode($_GET['id']));
                  $type = sanitize(rawurldecode($_GET['type']));

                  if (!is_numeric($type) || is_nan($type) || ($type !== '1' && $type !== '2' && $type !== '3')) {
                        http_response_code(400);
                        echo json_encode(['error' => '`Coupon Type` data type invalid!']);
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

                  $queryResult = [];

                  if ($type === '1') {
                        $stmt = $conn->prepare('select discount.name,eventDiscount.discount,eventDiscount.startDate,eventDiscount.endDate,eventDiscount.applyForAll from discount join eventDiscount on discount.id=eventDiscount.id where discount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select discount.name,eventDiscount.discount,eventDiscount.startDate,eventDiscount.endDate,eventDiscount.applyForAll from discount join eventDiscount on discount.id=eventDiscount.id where discount.id=?` preparation failed!']);
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
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                        } else {
                              $result = $result->fetch_assoc();
                              $queryResult = $result;
                        }
                        $stmt->close();

                        if (!$queryResult['applyForAll']) {
                              $stmt = $conn->prepare('select book.id,book.name,book.edition from book join eventApply on eventApply.bookID=book.id where eventApply.eventID=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select book.id,book.name,book.edition from book join eventApply on eventApply.bookID=book.id where eventApply.eventID=?` preparation failed!']);
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
                              }
                              $result = $stmt->get_result();
                              $appliedBooks=[];
                              while ($row = $result->fetch_assoc()) {
                                    $row['edition']=convertToOrdinal($row['edition']);
                                    $appliedBooks[]=$row;
                              }
                              $queryResult['bookApply']= $appliedBooks;
                              $stmt->close();
                        }
                  } else if ($type === '2') {
                        $stmt = $conn->prepare('select discount.name,customerDiscount.discount,customerDiscount.point from discount join customerDiscount on discount.id=customerDiscount.id where discount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select discount.name,customerDiscount.discount,customerDiscount.point from discount join customerDiscount on discount.id=customerDiscount.id where discount.id=?` preparation failed!']);
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
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                        } else {
                              $result = $result->fetch_assoc();
                              $queryResult = $result;
                        }
                        $stmt->close();
                  } else if ($type === '3') {
                        $stmt = $conn->prepare('select discount.name,referrerDiscount.discount,referrerDiscount.numberOfPeople from discount join referrerDiscount on discount.id=referrerDiscount.id where discount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select discount.name,referrerDiscount.discount,referrerDiscount.numberOfPeople from discount join referrerDiscount on discount.id=referrerDiscount.id where discount.id=?` preparation failed!']);
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
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                        } else {
                              $result = $result->fetch_assoc();
                              $queryResult = $result;
                        }
                        $stmt->close();
                  }

                  echo json_encode(['query_result' => $queryResult]);

                  // Close connection
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