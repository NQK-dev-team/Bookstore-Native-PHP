
<?php
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['id'], $_GET['rating'], $_GET['limit'], $_GET['showAll'])) {
            try {
                  $id = sanitize(rawurldecode($_GET['id']));
                  $rating = sanitize(rawurldecode($_GET['rating']));
                  $limit = sanitize(rawurldecode($_GET['limit']));
                  $showAll = filter_var(sanitize(rawurldecode($_GET['showAll'])), FILTER_VALIDATE_BOOLEAN);

                  if (!$rating) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing rating filter!']);
                        exit;
                  } else if ($rating !== "all" && $rating !== '1' && $rating !== '2' && $rating !== '3' && $rating !== '4' && $rating !== '5') {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid rating filter!']);
                        exit;
                  }

                  if (!$limit) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing limit!']);
                        exit;
                  } else if (!is_numeric($limit) || is_nan($limit) || $limit < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid limit!']);
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

                  $stmt = $conn->prepare('select * from book where id=? and status=true');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from book where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Book not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  if ($rating === 'all') {
                        $stmt = $conn->prepare('select count(star) as result from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(star) as result from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $total = $row['result'];
                        $stmt->close();

                        if (!$showAll) {
                              $stmt = $conn->prepare('select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name limit ? offset 0');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name limit ? offset 0` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('si', $id, $limit);
                        } else {
                              $stmt = $conn->prepare('select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                                    where bookID=? order by ratingTime desc,star desc,name` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('s', $id);
                        }
                  } else {
                        $stmt = $conn->prepare('select count(star) as result from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select count(star) as result from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('si', $id, $rating);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $total = $row['result'];
                        $stmt->close();

                        if (!$showAll) {
                              $stmt = $conn->prepare('select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name limit ? offset 0');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name limit ? offset 0` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('sii', $id, $rating, $limit);
                        } else {
                              $stmt = $conn->prepare('select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select star,comment,name,imagePath,gender,ratingTime from rating join customer on customer.id=rating.customerID join appUser on appUser.id=customer.id
                              where bookID=? and star=? order by ratingTime desc,name` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('si', $id, $rating);
                        }
                  }
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $ratings = [];
                  while ($row = $result->fetch_assoc()) {
                        if (!$row['imagePath']) {
                              if ($row['gender'] === 'M') {
                                    $row['imagePath'] = '/image/default_male.jpeg';
                              } else if ($row['gender'] === 'F') {
                                    $row['imagePath'] = '/image/default_female.jpg';
                              } else if ($row['gender'] === 'O') {
                                    $row['imagePath'] = '/image/default_other.png';
                              }
                        } else
                              $row['imagePath'] = "https://{$_SERVER['HTTP_HOST']}/data/user/customer/" . normalizeURL(rawurlencode($row['imagePath']));
                        unset($row['gender']);
                        $row['ratingTime'] = formatOrderTime($row['ratingTime']);
                        $ratings[] = $row;
                  }
                  $stmt->close();

                  $conn->close();
                  echo json_encode(['query_result' => [$ratings, $total]]);
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