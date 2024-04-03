
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (
            isset($_GET['start']) &&
            isset($_GET['end'])
      ) {
            try {
                  $start = sanitize(rawurldecode($_GET['start']));
                  $end = sanitize(rawurldecode($_GET['end']));

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

                  $startDT = new DateTime($start, new DateTimeZone($_ENV['TIMEZONE']));
                  $startDT->setTime(0, 0, 0); // Set time to 00:00:00
                  $endDT = new DateTime($end, new DateTimeZone($_ENV['TIMEZONE']));
                  $endDT->setTime(0, 0, 0); // Set time to 00:00:00
                  $currentDate = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                  $currentDate->setTime(0, 0, 0); // Set time to 00:00:00

                  if ($startDT > $currentDate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Start date must be before or the same day as today!']);
                        exit;
                  }

                  if ($endDT > $currentDate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'End date must be before or the same day as today!']);
                        exit;
                  }

                  if ($startDT > $endDT) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Start date must be before or the same day as end date!']);
                        exit;
                  }

                  $start = $start . ' 00:00:00';
                  $end = $end . ' 23:59:59';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select category.name,sum(totalSold) as finalTotalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and purchaseTime>=? and purchaseTime<=? group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and purchaseTime>=? and purchaseTime<=? group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name order by name');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select category.name,sum(totalSold) as finalTotalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join book on book.id=physicalOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and purchaseTime>=? and purchaseTime<=? group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join book on book.id=fileOrderContain.bookID and book.status=true join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and purchaseTime>=? and purchaseTime<=? group by bookID
) as combined join belong on belong.bookID=combined.bookID join category on category.id=belong.categoryID group by category.name order by name` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ssss', $start, $end, $start, $end);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                  }

                  $queryResult = [];

                  $result = $stmt->get_result();
                  while ($row = $result->fetch_assoc()) {
                        $queryResult[] = $row;
                  }

                  $conn->close();

                  echo json_encode(['query_result' => $queryResult]);
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