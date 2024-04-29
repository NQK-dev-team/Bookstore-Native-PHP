
<?php
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

            $stmt = $conn->prepare('select book.id,name,edition,imagePath from book join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id where result.discount!=0 and book.status=true order by result.discount desc limit 10;');
            if (!$stmt) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Query `select book.id,name,edition,imagePath from book join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id where result.discount!=0 and book.status=true order by result.discount desc limit 10;` preparation failed!']);
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

                  $sub_stmt = $conn->prepare('select price from physicalCopy where id=?');
                  if (!$sub_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select price from physicalCopy where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $sub_stmt->bind_param('s', $id);
                  if (!$sub_stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $sub_stmt->error]);
                        $sub_stmt->close();
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $sub_result = $sub_stmt->get_result();
                  if ($sub_result->num_rows === 0) {
                        $queryResult[$idx]['physicalPrice'] = null;
                  } else {
                        $sub_row = $sub_result->fetch_assoc();
                        $queryResult[$idx]['physicalPrice'] = $sub_row['price'];
                  }
                  $sub_stmt->close();

                  $sub_stmt = $conn->prepare('select price from fileCopy where id=?');
                  if (!$sub_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select price from fileCopy where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $sub_stmt->bind_param('s', $id);
                  if (!$sub_stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $sub_stmt->error]);
                        $sub_stmt->close();
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $sub_result = $sub_stmt->get_result();
                  if ($sub_result->num_rows === 0) {
                        $queryResult[$idx]['filePrice'] = null;
                  } else {
                        $sub_row = $sub_result->fetch_assoc();
                        $queryResult[$idx]['filePrice'] = $sub_row['price'];
                  }
                  $sub_stmt->close();

                  $sub_stmt = $conn->prepare('select combined.discount from (
						select distinct discount.id,eventDiscount.discount,1 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=?
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id limit 1');

                  if (!$sub_stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select combined.discount from (
						select distinct discount.id,eventDiscount.discount,1 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=?
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id limit 1` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $sub_stmt->bind_param('s', $id);
                  if (!$sub_stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $sub_stmt->error]);
                        $sub_stmt->close();
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $sub_result = $sub_stmt->get_result();
                  if ($sub_result->num_rows === 0) {
                        $queryResult[$idx]['discount'] = null;
                  } else {
                        $sub_row = $sub_result->fetch_assoc();
                        $queryResult[$idx]['discount'] = $sub_row['discount'];
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