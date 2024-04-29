
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/check_https.php';

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

            $stmt = $conn->prepare('select bookID as id,sum(totalSold) as finalTotalSold,name,edition,isbn,publisher,publishDate,imagePath from (
select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join book on book.id=combined.bookID and book.status=true group by bookID order by finalTotalSold desc,name limit 5');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select bookID as id,sum(totalSold) as finalTotalSold,name,edition,isbn,publisher,publishDate,imagePath from (
select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join book on book.id=combined.bookID and book.status=true group by bookID order by finalTotalSold desc,name limit 5` preparation failed!']);
                  $conn->close();
                  exit;
            }
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
            }
            $result = $stmt->get_result();

            $queryResult = [];
            $idx = 0;
            while ($row = $result->fetch_assoc()) {
                  $host = $_SERVER['HTTP_HOST'];
                  $row['imagePath'] = (isSecure() ? 'https' : 'http') . "://$host/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
                  $row['edition'] = convertToOrdinal($row['edition']);
                  $row['isbn'] = formatISBN($row['isbn']);
                  $row['publishDate'] = MDYDateFormat($row['publishDate']);
                  $queryResult[] = $row;

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
                        $queryResult[$idx]['author'] = [];
                  } else {
                        while ($sub_row = $sub_result->fetch_assoc()) {
                              $queryResult[$idx]['author'][] = $sub_row['authorName'];
                        }
                  }
                  $sub_stmt->close();

                  $sub_stmt = $conn->prepare('select category.name from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                  if (!$sub_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select category.name from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id` preparation failed!']);
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
                        $queryResult[$idx]['category'] = [];
                  } else {
                        while ($sub_row = $sub_result->fetch_assoc()) {
                              $queryResult[$idx]['category'][] = $sub_row['name'];
                        }
                  }
                  $sub_stmt->close();

                  $idx++;
            }
            $stmt->close();

            $conn->close();

            echo json_encode(['query_result' => $queryResult]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>