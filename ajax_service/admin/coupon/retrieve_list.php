
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/checker.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['entry']) && isset($_GET['offset']) && isset($_GET['search']) && isset($_GET['status']) && isset($_GET['type'])) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $search = sanitize(rawurldecode($_GET['search']));
                  $status = filter_var(sanitize(rawurldecode($_GET['status'])), FILTER_VALIDATE_BOOLEAN);
                  $type = sanitize(rawurldecode($_GET['type']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of entries of coupons!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of entries of coupons invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing coupon list number!']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Coupon list number invalid!']);
                        exit;
                  }

                  if (!$type) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing coupon type!']);
                        exit;
                  } else if (!is_numeric($type) || is_nan($type) || ($type !== '1' && $type !== '2' && $type !== '3')) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Coupon type invalid!']);
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
                  $totalEntries = 0;
                  $search = '%' . $search . '%';
                  $offset = ($offset - 1) * $entry;

                  if ($type === '1') {
                        $stmt = $conn->prepare("SELECT discount.id,discount.name,eventDiscount.startDate,eventDiscount.endDate,eventDiscount.discount,eventDiscount.applyForAll FROM eventDiscount join discount on eventDiscount.id=discount.id where discount.status=? and discount.name like ? order by startDate desc,discount,name,endDate desc,id limit ? offset ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT discount.id,discount.name,eventDiscount.startDate,eventDiscount.endDate,eventDiscount.discount,eventDiscount.applyForAll FROM eventDiscount join discount on eventDiscount.id=discount.id where discount.status=? and discount.name like ? order by startDate desc,discount,name,endDate desc,id limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('isii', $status, $search, $entry, $offset);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                              if ($status)
                                    $row['status'] = isInPeriod($row['startDate'], $row['endDate']);
                              $row['startDate'] = MDYDateFormat($row['startDate']);
                              $row['endDate'] = MDYDateFormat($row['endDate']);
                              $sub_stmt = $conn->prepare("select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result");
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $row['id']);
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
                              $row['deletable'] = !$sub_result->fetch_assoc()['result'];
                              $sub_stmt->close();

                              if (!$row['applyForAll']) {
                                    $sub_stmt = $conn->prepare('SELECT book.id,book.name,book.edition,book.status FROM book join eventApply on book.id=eventApply.bookID where eventApply.eventID=? order by book.name,book.edition,book.id');
                                    if (!$sub_stmt) {
                                          http_response_code(500);
                                          echo json_encode(['error' => 'Query `SELECT book.id,book.name,book.edition,book.status FROM book join eventApply on book.id=eventApply.bookID where eventApply.eventID=? order by book.name,book.edition,book.id` preparation failed!']);
                                          $conn->close();
                                          exit;
                                    }
                                    $sub_stmt->bind_param('s', $row['id']);
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
                                    if ($sub_result->num_rows > 0) {
                                          while ($sub_row = $sub_result->fetch_assoc()) {
                                                $sub_row['edition'] = convertToOrdinal($sub_row['edition']);
                                                $row['applyFor'][] = $sub_row;
                                          }
                                    } else {
                                          $row['applyFor'] = [];
                                    }
                              } else {
                                    $row['applyFor'] = true;
                              }

                              $queryResult[] = $row;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare("select count(*) as total FROM eventDiscount join discount on eventDiscount.id=discount.id where discount.status=? and discount.name like ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(*) as total FROM eventDiscount join discount on eventDiscount.id=discount.id where discount.status=? and discount.name like ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('is', $status, $search);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $totalEntries = $result->fetch_assoc()['total'];
                        $stmt->close();
                  } else if ($type === '2') {
                        $stmt = $conn->prepare("select discount.id,discount.name,customerDiscount.point,customerDiscount.discount from discount join customerDiscount on customerDiscount.id=discount.id where discount.status=? and discount.name like ? order by customerDiscount.discount limit ? offset ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select discount.id,discount.name,customerDiscount.point,customerDiscount.discount from discount join customerDiscount on customerDiscount.id=discount.id where discount.status=? and discount.name like ? order by customerDiscount.discount limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('isii', $status, $search, $entry, $offset);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                              $sub_stmt = $conn->prepare("select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result");
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $row['id']);
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
                              $row['deletable'] = !$sub_result->fetch_assoc()['result'];
                              $sub_stmt->close();
                              $queryResult[] = $row;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare("select count(*) as total from discount join customerDiscount on customerDiscount.id=discount.id where discount.status=? and discount.name like ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(*) as total from discount join customerDiscount on customerDiscount.id=discount.id where discount.status=? and discount.name like ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('is', $status, $search);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $totalEntries = $result->fetch_assoc()['total'];
                        $stmt->close();
                  } else if ($type === '3') {
                        $stmt = $conn->prepare("select discount.id,discount.name,referrerDiscount.discount,referrerDiscount.numberOfPeople from discount join referrerDiscount on referrerDiscount.id=discount.id where discount.status=? and discount.name like ? order by referrerDiscount.discount limit ? offset ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select discount.id,discount.name,referrerDiscount.discount,referrerDiscount.numberOfPeople from discount join referrerDiscount on referrerDiscount.id=discount.id where discount.status=? and discount.name like ? order by referrerDiscount.discount limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('isii', $status, $search, $entry, $offset);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                              $sub_stmt = $conn->prepare("select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result");
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=?) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $row['id']);
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
                              $row['deletable'] = !$sub_result->fetch_assoc()['result'];
                              $queryResult[] = $row;
                              $sub_stmt->close();
                        }
                        $stmt->close();

                        $stmt = $conn->prepare("select count(*) as total from discount join referrerDiscount on referrerDiscount.id=discount.id where discount.status=? and discount.name like ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(*) as total from discount join referrerDiscount on referrerDiscount.id=discount.id where discount.status=? and discount.name like ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('is', $status, $search);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $totalEntries = $result->fetch_assoc()['total'];
                        $stmt->close();
                  }

                  echo json_encode(['query_result' => [$queryResult, $totalEntries]]);

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