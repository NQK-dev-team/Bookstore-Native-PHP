
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

      $stmt = $conn->prepare('select exists(select * from appUser where id=?) as result;');
      $stmt->bind_param('s', $id);
      $stmt->execute();

      $result = $stmt->get_result();
      $result = $result->fetch_assoc();

      return $result['result'] === 1;
}
?>