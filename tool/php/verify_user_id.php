
<?php
require_once __DIR__ . '/../../config/db_connection.php';

function verifyUserID($id)
{
      global $db_host, $db_user, $db_password, $db_database, $db_port;
      // Connect to MySQL
      $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

      // Check connection
      if (!$conn) {
            return false;
      }

      $stmt = $conn->prepare('select exists(select * from appUser join admin on admin.id=appUser.id where admin.id=?) as result;');
      $stmt->bind_param('s', $id);
      $isSuccess = $stmt->execute();

      if (!$isSuccess) {
            $stmt->close();
            $conn->close();
            return false;
      }

      $result = $stmt->get_result();
      if ($result->num_rows !== 1) {
            $stmt->close();
            $conn->close();
            return false;
      }
      $result = $result->fetch_assoc();
      $result = $result['result'];
      $stmt->close();

      if ($result === 1) {
            $conn->close();
            return true;
      }

      $stmt = $conn->prepare('select exists(select * from appUser join customer on customer.id=appUser.id where customer.id=? and status=true) as result;');
      $stmt->bind_param('s', $id);
      $isSuccess = $stmt->execute();
      if (!$isSuccess) {
            $stmt->close();
            $conn->close();
            return false;
      }
      $result = $stmt->get_result();
      if ($result->num_rows !== 1) {
            $stmt->close();
            $conn->close();
            return false;
      }
      $result = $result->fetch_assoc();
      $result = $result['result'];
      $stmt->close();

      $conn->close();

      return $result === 1;
}
?>