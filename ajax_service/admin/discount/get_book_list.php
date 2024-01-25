
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
      if (isset($_GET['entry'], $_GET['offset'], $_GET['search'], $_GET['category'])) {
            try {


                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $search = sanitize(rawurldecode($_GET['search']));
                  $category = sanitize(rawurldecode($_GET['category']));

                  if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => '`Number Of Entries` data type invalid!']);
                        exit;
                  }

                  if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => '`List Number` data type invalid!']);
                        exit;
                  }

                  $queryResult = [];
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

                  $stmt = $conn->prepare('select distinct book.id,book.name,book.edition
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=true and book.name like ? and category.name like ?
                  order by book.name,book.id limit ? offset ?');
                  $stmt->bind_param('ssii', $search, $category, $entry, $offset);
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();

                        $idx = 0;
                        while ($row = $result->fetch_assoc()) {
                              $row['edition'] = convertToOrdinal($row['edition']);
                              $queryResult[] = $row;

                              $id = $row['id'];

                              $sub_stmt = $conn->prepare('select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
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

                              $idx++;
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select count(distinct book.id) as totalBook
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=true and book.name like ? and category.name like ?');
                  $stmt->bind_param('ss', $search, $category);
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