
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';


if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
      parse_str(file_get_contents('php://input'), $_PATCH);
      if (isset($_PATCH['id']) && isset($_PATCH['status']) && isset($_PATCH['type'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_PATCH['id']));
                  $status = filter_var(sanitize($_PATCH['status']), FILTER_VALIDATE_BOOLEAN);
                  $type = sanitize(rawurldecode($_PATCH['type']));

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

                  if ($type === '1') {
                        $stmt = $conn->prepare('select * from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?` preparation failed!']);
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
                              exit;
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        if ($status)
                        {
                              $stmt = $conn->prepare('select discount.name from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select discount.name from eventDiscount join discount on discount.id=eventDiscount.id where eventDiscount.id=?` preparation failed!']);
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
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              $name = $result['name'];
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from discount where name=? and id!=? and status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discount where name=? and id!=? and status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $name, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current coupon name has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();
                        }
                  } else if ($type === '2') {
                        $stmt = $conn->prepare('select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?` preparation failed!']);
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
                              exit;
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        if ($status) {
                              $stmt = $conn->prepare('select discount.name,customerDiscount.discount,point from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select discount.name,customerDiscount.discount,point from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?` preparation failed!']);
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
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              $discount = $result['discount'];
                              $point = $result['point'];
                              $name = $result['name'];
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from discount where name=? and id!=? and status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discount where name=? and id!=? and status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $name, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current coupon name has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.discount-?)<10e-9 and customerDiscount.id!=? and discount.status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.discount-?)<10e-9 and customerDiscount.id!=? and discount.status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ds', $discount, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current discount percentage value has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-?)<10e-9 and customerDiscount.id!=? and discount.status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-?)<10e-9 and customerDiscount.id!=? and discount.status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ds', $point, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current accumulated point milestone has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();
                        }
                  } else if ($type === '3') {
                        $stmt = $conn->prepare('select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?` preparation failed!']);
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
                              exit;
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              echo json_encode(['error' => 'Coupon not found!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        if ($status) {
                              $stmt = $conn->prepare('select discount.name,referrerDiscount.discount,numberOfPeople from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select discount.name,referrerDiscount.discount,numberOfPeople from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?` preparation failed!']);
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
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              $discount = $result['discount'];
                              $numberOfPeople = $result['numberOfPeople'];
                              $name = $result['name'];
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from discount where name=? and id!=? and status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from discount where name=? and id!=? and status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $name, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current coupon name has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where abs(referrerDiscount.discount-?)<10e-9 and referrerDiscount.id!=? and discount.status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where abs(referrerDiscount.discount-?)<10e-9 and referrerDiscount.id!=? and discount.status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ds', $discount, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current discount percentage value has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=? and referrerDiscount.id!=? and discount.status=true) as result');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=? and referrerDiscount.id!=? and discount.status=true) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('is', $numberOfPeople, $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();
                              if ($result['result']) {
                                    echo json_encode(['error' => 'Can not activate this coupon, current number of people milestone has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();
                        }
                  }

                  $stmt = $conn->prepare('update discount set status=? where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update discount set status=? where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('is', $status, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        if ($stmt->affected_rows > 1) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Updated more than one discount event coupon!']);
                        } else {
                              echo json_encode(['query_result' => true]);
                        }
                  }
                  $stmt->close();
                  
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