
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (
            isset($_GET['entry']) &&
            isset($_GET['offset']) &&
            isset($_GET['status']) &&
            isset($_GET['search'])
      ) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $status = filter_var(sanitize(rawurldecode($_GET['status'])), FILTER_VALIDATE_BOOLEAN);
                  $search = sanitize(rawurldecode($_GET['search']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of entries of customers!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of entries of customers invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing customer list number']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Customer list number invalid!']);
                        exit;
                  }

                  $queryResult = [];
                  $search = '%' . $search . '%';
                  $offset = ($offset - 1) * $entry;

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = $conn->prepare('select name,email,phone,dob,gender,point,cardNumber,address,appUser.id,deleteTime from appUser join customer on customer.id=appUser.id where status=? and (name like ? or email like ? or phone like ? or cardNumber like ?) order by point desc,name,email,customer.id limit ? offset ?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select name,email,phone,dob,gender,point,cardNumber,address,appUser.id,deleteTime from appUser join customer on customer.id=appUser.id where status=? and (name like ? or email like ? or phone like ? or cardNumber like ?) order by point desc,name,email,customer.id limit ? offset ?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('issssii', $status,  $search, $search, $search, $search, $entry, $offset);
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                              $row['email'] = $row['email'] ? $row['email'] : 'N/A';
                              $row['phone'] = $row['phone'] ? $row['phone'] : 'N/A';
                              $row['dob'] = MDYDateFormat($row['dob']);
                              $row['address'] = $row['address'] ? $row['address'] : 'N/A';
                              $row['gender'] = $row['gender'] === 'M' ? 'Male' : ($row['gender'] === 'F' ? 'Female' : 'Other');
                              $row['point'] = round($row['point'], 2);
                              $row['cardNumber'] = $row['cardNumber'] ? $row['cardNumber'] : 'N/A';

                              $queryResult[] = $row;
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select count(*) as total from appUser join customer on customer.id=appUser.id where status=? and (name like ? or email like ? or phone like ? or cardNumber like ?)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select count(*) as total from appUser join customer on customer.id=appUser.id where status=? and (name like ? or email like ? or phone like ? or cardNumber like ?)` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('issss', $status, $search, $search, $search, $search);
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
                        $totalEntries = $result['total'];
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