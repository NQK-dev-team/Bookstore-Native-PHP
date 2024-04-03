
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../config/db_connection.php';

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

            $stmt = $conn->prepare('select category.name,sum(totalSold) as finalTotalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name order by name');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select category.name,sum(totalSold) as finalTotalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name order by name` preparation failed!']);
                  $conn->close();
                  exit;
            }
            if (!$stmt->execute()) {
                  http_response_code(500);
                  echo json_encode(['error' => $stmt->error]);
                  $stmt->close();
                  $conn->close();
            }

            $data = [];

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                  $data[] = $row;
            }

            $stmt->close();

            $conn->close();

            echo json_encode(['query_result' => $data]);
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>