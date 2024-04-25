
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['search'])) {
            try {
                  $search = sanitize(rawurldecode($_GET['search']));

                  $search = '%' . $search . '%';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $query_result = [];

                  if ($search === '%%') {
                        $stmt = $conn->prepare('select category.name,COALESCE(superCombined.totalSold,0) as totalSold from category left join (select category.name,sum(totalSold) as totalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name) as superCombined on superCombined.name=category.name order by totalSold desc,name limit 5;');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select category.name,COALESCE(superCombined.totalSold,0) as totalSold from category left join (select category.name,sum(totalSold) as totalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name) as superCombined on superCombined.name=category.name order by totalSold desc,name limit 5;` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                  } else {
                        $stmt = $conn->prepare('select name from category where name like ?;');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select name from category where name like ?;` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $search);
                  }
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                                    unset($row['totalSold']);
                                    $query_result[] = $row;
                              }
                        }
                  }
                  echo json_encode(['query_result' => $query_result]);
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