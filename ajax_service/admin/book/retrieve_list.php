
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (
            isset($_GET['entry']) &&
            isset($_GET['offset']) &&
            isset($_GET['status']) &&
            isset($_GET['search']) &&
            isset($_GET['category'])
      ) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $status = filter_var(sanitize(rawurldecode($_GET['status'])), FILTER_VALIDATE_BOOLEAN);
                  $search = sanitize(rawurldecode($_GET['search']));
                  $category = sanitize(rawurldecode($_GET['category']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of entries of books!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of entries of books invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing book list number']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book list number invalid!']);
                        exit;
                  }

                  $queryResult = [];
                  $isbnSearch = '%' . str_replace('-', '', $search) . '%';
                  $search = '%' . $search . '%';
                  $offset = ($offset - 1) * $entry;
                  $category = '%' . $category . '%';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = null;

                  if ($category === '%%') {
                        $stmt = $conn->prepare('(select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id limit ? offset ?)
                  
                  union
                  
                  (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?)
                  order by book.name,book.id limit ? offset ?)');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `(select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id limit ? offset ?)
                  
                  union
                  
                  (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?)
                  order by book.name,book.id limit ? offset ?)` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('issssiiisssii', $status, $search, $isbnSearch,  $search, $category, $entry, $offset, $status, $search, $isbnSearch,  $search, $entry, $offset);
                  } else {
                        $stmt = $conn->prepare('(select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id limit ? offset ?)');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `(select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id limit ? offset ?)` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('issssii', $status, $search, $isbnSearch,  $search, $category, $entry, $offset);
                  }
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();

                        $idx = 0;
                        while ($row = $result->fetch_assoc()) {
                              $host = $_SERVER['HTTP_HOST'];
                              $row['imagePath'] = "src=\"https://$host/data/book/" . normalizeURL(rawurlencode($row['imagePath'])) . "\"";
                              $row['ageRestriction'] = $row['ageRestriction'] ? $row['ageRestriction'] : 'N/A';
                              $row['edition'] = convertToOrdinal($row['edition']);
                              $row['isbn'] = formatISBN($row['isbn']);
                              $row['publishDate'] = MDYDateFormat($row['publishDate']);
                              $row['description'] = $row['description'] ? $row['description'] : 'N/A';
                              $queryResult[] = $row;

                              $id = $row['id'];

                              $sub_stmt = $conn->prepare('select (exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select (exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('ss', $id, $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              $sub_result = $sub_result->fetch_assoc();
                              $queryResult[$idx]['can_delete'] = !$sub_result['result'];

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

                              $sub_stmt = $conn->prepare('select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id` preparation failed!']);
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
                                          $temp = [];
                                          $temp['name'] = $sub_row['name'];
                                          $temp['description'] = $sub_row['description'];
                                          $queryResult[$idx]['category'][] = $temp;
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select price,inStock from physicalCopy where id=?` preparation failed!']);
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
                                    $queryResult[$idx]['physicalCopy'] = [];
                              } else if ($sub_result->num_rows === 1) {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $queryResult[$idx]['physicalCopy']['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $queryResult[$idx]['physicalCopy']['inStock'] = $sub_row['inStock'] ? $sub_row['inStock'] : "N/A";
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select price,filePath from fileCopy where id=?` preparation failed!']);
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
                                    $queryResult[$idx]['fileCopy'] = [];
                              } else if ($sub_result->num_rows === 1) {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $sub_row['filePath'] = $sub_row['filePath'] ? "href=\"https://$host/data/book/" . normalizeURL(rawurlencode($sub_row['filePath'])) . "\"" : '';

                                          $queryResult[$idx]['fileCopy']['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $queryResult[$idx]['fileCopy']['filePath'] = $sub_row['filePath'];
                                    }
                              }
                              $sub_stmt->close();

                              $idx++;
                        }
                  }
                  $stmt->close();

                  if ($category === '%%') {
                        $stmt = $conn->prepare('select count(*) as totalBook from(
                        (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id)
                  
                  union
                  
                  (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?)
                  order by book.name,book.id)
                  ) as combined');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(*) as totalBook from(
                        (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id)
                  
                  union
                  
                  (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?)
                  order by book.name,book.id)
                  ) as combined` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('issssisss', $status, $search, $isbnSearch, $search, $category, $status, $search, $isbnSearch, $search);
                  } else {
                        $stmt = $conn->prepare('select count(*) as totalBook from(
                        (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id)
                  ) as combined');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(*) as totalBook from(
                        (select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ? or author.authorName like ?) and category.name like ?
                  order by book.name,book.id)
                  ) as combined` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('issss', $status, $search, $isbnSearch, $search, $category);
                  }
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  } else {
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        $totalEntries = $result['totalBook'];
                  }
                  $stmt->close();

                  echo json_encode(['query_result' => [$queryResult, $totalEntries]]);

                  // Close connection
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