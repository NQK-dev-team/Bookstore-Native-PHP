
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['search'], $_GET['category'], $_GET['author'], $_GET['publisher'], $_GET['limit'], $_GET['offset'], $_GET['mode'])) {
            try {
                  $search = '%' . sanitize(rawurldecode($_GET['search'])) . '%';
                  $category = sanitize(rawurldecode($_GET['category']));
                  $category = $category ? $category : '%';
                  $author = sanitize(rawurldecode($_GET['author']));
                  $author = $author ? $author : '%';
                  $publisher = sanitize(rawurldecode($_GET['publisher']));
                  $publisher = $publisher ? $publisher : '%';
                  $entry = sanitize(rawurldecode($_GET['limit']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $mode = sanitize(rawurldecode($_GET['mode']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing entry value!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Entry value invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing offset value!']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'List offset invalid!']);
                        exit;
                  }

                  if (!$mode) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing mode value!']);
                        exit;
                  } else if (!in_array($mode, ['1', '2', '3', '4', '5'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid mode value!']);
                        exit;
                  }

                  $offset = ($offset - 1) * $entry;

                  $queryResult = [];

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  if ($mode === '1') {
                        $stmt = $conn->prepare("SELECT distinct book.name,book.edition,book.id,book.imagePath from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ? order by book.name,book.edition limit ? offset ?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT distinct book.name,book.edition,book.id,book.imagePath from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ? order by book.name,book.edition limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param("ssssii", $search, $category, $author, $publisher, $entry, $offset);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                  } else if ($mode === '2') {
                        $stmt = $conn->prepare("SELECT distinct book.id,book.name,edition,imagePath from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id
                    where book.status=true and result.discount!=0 and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
                    order by result.discount desc,book.name,book.edition
                    limit ? offset ?;");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT distinct book.id,book.name,edition,imagePath from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id
                    where book.status=true and result.discount!=0 and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
                    order by result.discount desc,book.name,book.edition
                    limit ? offset ?;` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param("ssssii", $search, $category, $author, $publisher, $entry, $offset);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                  } else if ($mode === '3') {
                        $stmt = $conn->prepare("SELECT distinct book.id,book.name,book.edition,book.imagePath from book join (SELECT combined.bookID,sum(totalSold) as totalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined
join book on book.id=combined.bookID and book.status=true
group by combined.bookID order by sum(totalSold) desc) as result on book.id=result.bookID
join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
order by result.totalSold desc,book.name,book.edition
limit ? offset ?;");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT distinct book.id,book.name,book.edition,book.imagePath from book join (SELECT combined.bookID,sum(totalSold) as totalSold from (
select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
union
select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and week(purchaseTime,1)=week(curdate(),1) group by bookID
) as combined
join book on book.id=combined.bookID and book.status=true
group by combined.bookID order by sum(totalSold) desc) as result on book.id=result.bookID
join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
order by result.totalSold desc,book.name,book.edition limit ? offset ?;` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param("ssssii", $search, $category, $author, $publisher, $entry, $offset);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                  } else if ($mode === '4') {
                        $stmt = $conn->prepare("SELECT distinct book.name,book.edition,book.id,book.imagePath,coalesce(physicalCopy.price,0) as physicalPrice,coalesce(fileCopy.price,0) as filePrice,coalesce(result.discount,0) as discount from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        left join physicalCopy on physicalCopy.id=book.id
                        left join fileCopy on fileCopy.id=book.id
                        left join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id
				where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
                        order by coalesce(physicalCopy.price,0)*(100-result.discount)/100,coalesce(fileCopy.price,0)*(100-result.discount)/100,book.name,book.edition
                        limit ? offset ?;");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT distinct book.name,book.edition,book.id,book.imagePath,coalesce(physicalCopy.price,0) as price from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        left join physicalCopy on physicalCopy.id=book.id
                        where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ? order by price,book.name,book.edition limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param("ssssii", $search, $category, $author, $publisher, $entry, $offset);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                  } else if ($mode === '5') {
                        $stmt = $conn->prepare("SELECT distinct book.name,book.edition,book.id,book.imagePath,coalesce(physicalCopy.price,0) as physicalPrice,coalesce(fileCopy.price,0) as filePrice,coalesce(result.discount,0) as discount from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        left join physicalCopy on physicalCopy.id=book.id
                        left join fileCopy on fileCopy.id=book.id
                        left join (select book.id as bookID,coalesce(max(result.discount),0) as discount from book left join (select combined.bookID,combined.discount from (
						select distinct book.id as bookID, discount.id,eventDiscount.discount,1 as cardinal from book,eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct eventApply.bookID, discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id) as result on result.bookID=book.id group by book.id order by book.id) as result on result.bookID=book.id
				where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ?
                        order by coalesce(physicalCopy.price,0)*(100-result.discount)/100 desc,coalesce(fileCopy.price,0)*(100-result.discount)/100 desc,book.name,book.edition
                        limit ? offset ?;");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `SELECT distinct book.name,book.edition,book.id,book.imagePath,coalesce(physicalCopy.price,0) as price from book
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        left join physicalCopy on physicalCopy.id=book.id
                        where book.status=true and book.name like ? and category.name like ? and author.authorName like ? and book.publisher like ? order by price desc,book.name,book.edition limit ? offset ?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param("ssssii", $search, $category, $author, $publisher, $entry, $offset);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                  }

                  $idx = 0;
                  while ($row = $result->fetch_assoc()) {
                        unset($row['price']);
                        $host = $_SERVER['HTTP_HOST'];
                        $row['imagePath'] = "https://$host/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
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
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>