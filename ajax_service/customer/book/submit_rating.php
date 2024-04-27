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
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['id'], $_POST['rating'], $_POST['comment'])) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = sanitize(rawurldecode($_POST['id']));
                  $rating = sanitize(rawurldecode($_POST['rating']));
                  $comment = sanitize(rawurldecode($_POST['comment']));

                  if (!$rating) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing rating!']);
                        exit;
                  } else if (!is_numeric($rating) || is_nan($rating) || ($rating !== '1' && $rating !== '2' && $rating !== '3' && $rating !== '4' && $rating !== '5')) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid rating!']);
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

                  $stmt = $conn->prepare("SELECT exists(select * from rating where bookID=? and customerID=?) as result");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `SELECT exists(select * from rating where bookID=? and customerID=?) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $_SESSION['id']);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $haveCommented = $stmt->get_result()->fetch_assoc()['result'];
                  $stmt->close();

                  $conn->begin_transaction();
                  if ($haveCommented) {
                        $stmt = $conn->prepare("UPDATE rating set star=?, comment=? where bookID=? and customerID=?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `UPDATE rating set star=?, comment=? where bookID=? and customerID=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ssss', $rating, $comment, $id, $_SESSION['id']);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  } else {
                        $stmt = $conn->prepare("INSERT into rating (star, comment, bookID, customerID) values (?, ?, ?, ?)");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `INSERT into rating (star, comment, bookID, customerID) values (?, ?, ?, ?)` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ssss', $rating, $comment, $id, $_SESSION['id']);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $conn->commit();
                  $conn->close();

                  echo json_encode(['query_result' => true]);
            } catch (Exception $e) {
                  http_response_code(500);
                  echo json_encode(['error' => 'Internal Server Error!']);
            }
      } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters!']);
      }
} else {
      http_response_code(405);
      echo json_encode(['error' => 'Method Not Allowed!']);
}
