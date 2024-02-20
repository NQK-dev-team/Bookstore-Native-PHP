
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/delete_directory.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/notify_event.php';

function map($elem)
{
      return sanitize(rawurldecode($elem));
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      parse_str(file_get_contents('php://input'), $_PUT);
      try {
            if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                  http_response_code(403);
                  echo json_encode(['error' => 'CSRF token validation failed!']);
                  exit;
            }

            if (!isset($_PUT['type'], $_PUT['id'])) {
                  http_response_code(400);
                  echo json_encode(['error' => 'Missing coupon type parameter!']);
                  exit;
            }

            $id = sanitize(rawurldecode($_PUT['id']));
            $type = sanitize(rawurldecode($_PUT['type']));

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
                  if (!isset($_PUT['name']) || !isset($_PUT['discount']) || !isset($_PUT['start']) || !isset($_PUT['end']) || !isset($_PUT['bookApply']) || !isset($_PUT['allBook']) || !isset($_PUT['notifyAgain'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid data received!']);
                        exit;
                  }

                  $name = sanitize(rawurldecode($_PUT['name']));
                  $discount = sanitize(rawurldecode($_PUT['discount']));
                  $start = sanitize(rawurldecode($_PUT['start']));
                  $end = sanitize(rawurldecode($_PUT['end']));
                  $bookApply = $_PUT['bookApply'] ? array_map('map', explode(',', $_PUT['bookApply'])) : [];
                  $allBook = filter_var(sanitize(rawurldecode($_PUT['allBook'])), FILTER_VALIDATE_BOOLEAN);
                  $notifyAgain = filter_var(sanitize(rawurldecode($_PUT['notifyAgain'])), FILTER_VALIDATE_BOOLEAN);

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing coupon name!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Coupon name must be at most 255 characters long or less!']);
                        exit;
                  }

                  if (!$discount) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing discount percentage value!']);
                        exit;
                  } else if (!is_numeric($discount) || is_nan($discount) || $discount <= 0 || $discount > 100) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Discount percentage value invalid!']);
                        exit;
                  }

                  if (!$start) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing start date!']);
                        exit;
                  }

                  if (!$end) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing end date!']);
                        exit;
                  }

                  $startDT = new DateTime($start);
                  $startDT->setTime(0, 0, 0); // Set time to 00:00:00
                  $endDT = new DateTime($end);
                  $endDT->setTime(0, 0, 0); // Set time to 00:00:00

                  if ($startDT > $endDT) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Start date must be before or the same day as end date!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select eventDiscount.startDate,eventDiscount.endDate from discount join eventDiscount on discount.id=eventDiscount.id where discount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select eventDiscount.startDate,eventDiscount.endDate from discount join eventDiscount on discount.id=eventDiscount.id where discount.id=?` preparation failed!']);
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
                  $result = $result->fetch_assoc();
                  $oldStartDate = $result['startDate'];
                  $oldEndDate = $result['endDate'];
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from discount where id!=? and name=? and status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from discount where id!=? and name=? and status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $name);
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
                        echo json_encode(['error' => 'Can not update this coupon, current coupon name has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select (? < ? and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=? and customerOrder.purchaseTime<? and customerOrder.purchaseTime>=? and customerOrder.status=true)) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select (? < ? and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=? and customerOrder.purchaseTime<? and customerOrder.purchaseTime>=? and customerOrder.status=true)) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sssss', $oldStartDate, $start, $id, $start, $oldStartDate);
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
                        echo json_encode(['error' => 'Can not push up discount event start date, there are purchased order(s) that have already used this coupon on dates that are outside of the new start date you want to set!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select (? > ? and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=? and customerOrder.purchaseTime>? and customerOrder.purchaseTime<=? and customerOrder.status=true)) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select (? > ? and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=? and customerOrder.purchaseTime>? and customerOrder.purchaseTime<=? and customerOrder.status=true)) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sssss', $oldEndDate, $end, $id, $end, $oldEndDate);
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
                        echo json_encode(['error' => 'Can not shorten discount event end date, there are purchased order(s) that have already used this coupon on dates that are outside of the new end date you want to set!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $conn->begin_transaction();
                  $stmt = $conn->prepare('update discount join eventDiscount on discount.id=eventDiscount.id set discount.name=?,eventDiscount.discount=?,eventDiscount.startDate=?,eventDiscount.endDate=?,eventDiscount.applyForAll=? where eventDiscount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update discount join eventDiscount on discount.id=eventDiscount.id set discount.name=?,eventDiscount.discount=?,eventDiscount.startDate=?,eventDiscount.endDate=?,eventDiscount.applyForAll=? where eventDiscount.id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sdssis', $name, $discount, $start, $end, $allBook, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        $conn->rollback();
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  if (!$allBook) {
                        $stmt = $conn->prepare('delete from eventApply where eventID=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `delete from eventApply where eventID=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              $conn->rollback();
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('insert into eventApply(eventID,bookID) values(?,?)');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `dinsert into eventApply(eventID,bookID) values(?,?)` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        foreach ($bookApply as $bookID) {
                              $stmt->bind_param('ss', $id, $bookID);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    $conn->rollback();
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                        }
                        $stmt->close();
                  }

                  if ($notifyAgain) {
                        $stmt = $conn->prepare('update eventDiscount set isNotify=false where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update eventDiscount set isNotify=false where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              $conn->rollback();
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $conn->commit();
            } else if ($type === '2') {
                  if (!isset($_PUT['name']) || !isset($_PUT['discount']) || !isset($_PUT['point'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid data received!']);
                        exit;
                  }

                  $name = sanitize(rawurldecode($_PUT['name']));
                  $discount = sanitize(rawurldecode($_PUT['discount']));
                  $point = sanitize(rawurldecode($_PUT['point']));

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing coupon name!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Coupon name must be at most 255 characters long or less!']);
                        exit;
                  }

                  if (!$discount) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing discount percentage value!']);
                        exit;
                  } else if (!is_numeric($discount) || is_nan($discount) || $discount <= 0 || $discount > 100) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Discount percentage value invalid!']);
                        exit;
                  }

                  if (!$point) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing accumulated point value!']);
                        exit;
                  } else if (!is_numeric($point) || is_nan($point) || $point <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Accumulated point value invalid!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select * from discount join customerDiscount on discount.id=customerDiscount.id where discount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from discount join customerDiscount on discount.id=customerDiscount.id where discount.id=?` preparation failed!']);
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

                  $stmt = $conn->prepare('select exists(select * from discount where id!=? and name=? and status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from discount where id!=? and name=? and status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $name);
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
                        echo json_encode(['error' => 'Can not update this coupon, current coupon name has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where discount.id!=? and abs(customerDiscount.discount-?)<10e-9 and discount.status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where discount.id!=? and abs(customerDiscount.discount-?)<10e-9 and discount.status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sd', $id, $discount);
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
                        echo json_encode(['error' => 'Can not update this coupon, current discount percentage value has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where discount.id!=? and abs(customerDiscount.point-?)<10e-9 and discount.status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where discount.id!=? and abs(customerDiscount.point-?)<10e-9 and discount.status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sd', $id, $point);
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
                        echo json_encode(['error' => 'Can not update this coupon, current accumulated point milestone has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('update discount join customerDiscount on discount.id=customerDiscount.id set discount.name=?,customerDiscount.discount=?,customerDiscount.point=? where discount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update discount join customerDiscount on discount.id=customerDiscount.id set discount.name=?,customerDiscount.discount=?,customerDiscount.point=? where discount.id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sdds', $name, $discount, $point, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();
            } else if ($type === '3') {
                  if (!isset($_PUT['name']) || !isset($_PUT['discount']) || !isset($_PUT['people'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid data received!']);
                        exit;
                  }

                  $name = sanitize(rawurldecode($_PUT['name']));
                  $discount = sanitize(rawurldecode($_PUT['discount']));
                  $people = sanitize(rawurldecode($_PUT['people']));

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing coupon name!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Coupon name must be at most 255 characters long or less!']);
                        exit;
                  }

                  if (!$discount) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing discount percentage value!']);
                        exit;
                  } else if (!is_numeric($discount) || is_nan($discount) || $discount <= 0 || $discount > 100) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Discount percentage value invalid!']);
                        exit;
                  }

                  if (!$people) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of people value!']);
                        exit;
                  } else if (!is_numeric($people) || is_nan($people) || $people < 1) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of people value invalid!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select * from discount join referrerDiscount on discount.id=referrerDiscount.id where discount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from discount join referrerDiscount on discount.id=referrerDiscount.id where discount.id=?` preparation failed!']);
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

                  $stmt = $conn->prepare('select exists(select * from discount where id!=? and name=? and status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from discount where id!=? and name=? and status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $name);
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
                        echo json_encode(['error' => 'Can not update this coupon, current coupon name has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where discount.id!=? and abs(referrerDiscount.discount-?)<10e-9 and discount.status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where discount.id!=? and abs(referrerDiscount.discount-?)<10e-9 and discount.status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sd', $id, $discount);
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
                        echo json_encode(['error' => 'Can not update this coupon, current discount percentage value has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where discount.id!=? and referrerDiscount.numberOfPeople=? and discount.status=true) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where discount.id!=? and referrerDiscount.numberOfPeople=? and discount.status=true) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('si', $id, $people);
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
                        echo json_encode(['error' => 'Can not update this coupon, current number of people milestone has already been used in another coupon!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('update discount join referrerDiscount on discount.id=referrerDiscount.id set discount.name=?,referrerDiscount.discount=?,referrerDiscount.numberOfPeople=? where discount.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update discount join referrerDiscount on discount.id=referrerDiscount.id set discount.name=?,referrerDiscount.discount=?,referrerDiscount.numberOfPeople=? where discount.id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sdis', $name, $discount, $people, $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();
            }

            echo json_encode(['query_result' => true]);

            $conn->close();
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>