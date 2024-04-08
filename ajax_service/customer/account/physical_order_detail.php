
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
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['code'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $code = sanitize(rawurldecode(str_replace('-', '', $_GET['code'])));

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $finalResult = [];

                  $stmt = $conn->prepare('select imagePath,name,edition,isbn,publisher,publishDate,description,avgRating,amount,book.id,physicalCopy.price,destinationAddress
                  from book join physicalOrderContain on physicalOrderContain.bookID=book.id
                  join physicalOrder on physicalOrder.id=physicalOrderContain.orderID
                  join physicalCopy on physicalCopy.id=book.id
                  join customerOrder on customerOrder.id=physicalOrderContain.orderID
                  where customerOrder.orderCode=? and customerOrder.customerID=? and customerOrder.status=true order by name,book.id');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select imagePath,name,edition,isbn,publisher,publishDate,description,avgRating,amount,book.id,physicalCopy.price,destinationAddress
                  from book join physicalOrderContain on physicalOrderContain.bookID=book.id
                  join physicalOrder on physicalOrder.id=physicalOrderContain.orderID
                  join physicalCopy on physicalCopy.id=book.id
                  join customerOrder on customerOrder.id=physicalOrderContain.orderID
                  where customerOrder.orderCode=? and customerOrder.customerID=? and customerOrder.status=true order by name,book.id` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('ss', $code, $_SESSION['id']);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  while ($row = $result->fetch_assoc()) {
                        $row['isbn'] = formatISBN($row['isbn']);
                        $row['publishDate'] = MDYDateFormat($row['publishDate']);
                        $row['edition'] = convertToOrdinal($row['edition']);
                        $row['imagePath'] = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));

                        $sub_stmt = $conn->prepare('select authorName from author join book on author.bookID=book.id where author.bookID=?');
                        if (!$sub_stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select authorName from author join book on author.bookID=book.id where author.bookID=?` preparation failed!']);
                              $stmt->close();
                              $conn->close();
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
                        while ($sub_row = $sub_result->fetch_assoc()) {
                              $row['author'][] = $sub_row['authorName'];
                        }
                        $sub_stmt->close();

                        $sub_stmt = $conn->prepare('select category.name,category.description from category join belong on category.id=belong.categoryID join book on book.id=belong.bookID where book.id=?');
                        if (!$sub_stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select category.name,category.description from category join belong on category.id=belong.categoryID join book on book.id=belong.bookID where book.id=?` preparation failed!']);
                              $stmt->close();
                              $conn->close();
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
                        while ($sub_row = $sub_result->fetch_assoc()) {
                              $row['category'][] = $sub_row;
                        }
                        $sub_stmt->close();

                        $finalResult[] = $row;
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
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>