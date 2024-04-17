
<?php
require_once __DIR__ . '/../../config/db_connection.php';

function verifyUserID($id, $type)
{
      if (is_null($id)) return false;

      global $db_host, $db_user, $db_password, $db_database, $db_port;
      // Connect to MySQL
      $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

      // Check connection
      if (!$conn) {
            return false;
      }

      if ($type === 'admin') {
            $stmt = $conn->prepare('select exists(select * from appUser join admin on admin.id=appUser.id where admin.id=?) as result');
            if (!$stmt) {
                  http_response_code(500);
                  echo 'SQL statement error when verifying user ID!';
                  //echo json_encode(['error' => 'Query `select exists(select * from appUser join admin on admin.id=appUser.id where admin.id=?) as result` preparation failed!']);
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $id);
            $isSuccess = $stmt->execute();

            if (!$isSuccess) {
                  $stmt->close();
                  $conn->close();
                  return false;
            }

            $result = $stmt->get_result();
            $result = $result->fetch_assoc();
            $result = $result['result'];
            $stmt->close();
            $conn->close();

            return $result === 1;
      } else if ($type === 'customer') {
            $stmt = $conn->prepare('select exists(select * from appUser join customer on customer.id=appUser.id where customer.id=? and status=true) as result');
            if (!$stmt) {
                  http_response_code(500);
                  echo 'SQL statement error when verifying user ID!';
                  //echo json_encode(['error' => 'Query `select exists(select * from appUser join customer on customer.id=appUser.id where customer.id=? and status=true) as result` preparation failed!']);
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $id);
            $isSuccess = $stmt->execute();

            if (!$isSuccess) {
                  $stmt->close();
                  $conn->close();
                  return false;
            }
            $result = $stmt->get_result();
            $result = $result->fetch_assoc();
            $result = $result['result'];
            $stmt->close();
            $conn->close();

            return $result === 1;
      }

      return false;
}
?>