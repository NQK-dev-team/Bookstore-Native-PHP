
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
      if (isset($_PATCH['id'], $_PATCH['status'], $_PATCH['type'])) {
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
                  } else if ($type === '2') {
                        $stmt = $conn->prepare('select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.id=?');
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
                              $stmt = $conn->prepare('select discount,point from customerDiscount where customerDiscount.id=?');
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
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-?)<10e-9 and customerDiscount.id!=? and discount.status=true) as result');
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
                              if($result['result']){
                                    echo json_encode(['error' => 'Can not activate this coupon, current accumulated point milestone value has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where customerDiscount.discount=? and customerDiscount.id!=? and discount.status=true) as result');
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
                              if($result['result']){
                                    echo json_encode(['error' => 'Can not activate this coupon, current discount percentage value has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();
                        }
                  } else if ($type === '3') {
                        $stmt = $conn->prepare('select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.id=?');
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
                              $stmt = $conn->prepare('select discount,numberOfPeople from referrerDiscount where referrerDiscount.id=?');
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
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=? and referrerDiscount.id!=? and discount.status=true) as result');
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
                                    echo json_encode(['error' => 'Can not activate this coupon, current number of people milestone value has already been used in another coupon!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.discount=? and referrerDiscount.id!=? and discount.status=true) as result');
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
                        }
                  }

                  if ($type === '1') {
                        $stmt = $conn->prepare('update discount join eventDiscount on eventDiscount.id=discount.id set discount.status=? where eventDiscount.id=?');
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
                  } else if ($type === '2') {
                        $stmt = $conn->prepare('update discount join customerDiscount on customerDiscount.id=discount.id set discount.status=? where customerDiscount.id=?');
                        $stmt->bind_param('is', $status, $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                        } else {
                              if ($stmt->affected_rows > 1) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Updated more than one customer discount coupon!']);
                              } else {
                                    echo json_encode(['query_result' => true]);
                              }
                        }
                        $stmt->close();
                  } else if ($type === '3') {
                        $stmt = $conn->prepare('update discount join referrerDiscount on referrerDiscount.id=discount.id set discount.status=? where referrerDiscount.id=?');
                        $stmt->bind_param('is', $status, $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                        } else {
                              if ($stmt->affected_rows > 1) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Updated more than one referrer discount coupon!']);
                              } else {
                                    echo json_encode(['query_result' => true]);
                              }
                        }
                        $stmt->close();
                  }

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