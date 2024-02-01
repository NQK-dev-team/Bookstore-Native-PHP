
<?php
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../tool/php/send_mail.php';

try {

      $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

      if (!$conn) {
            $logMessage = date('Y-m-d H:i:s') . " - MySQL Connection Failed!\n";
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }
} catch (Exception $e) {
      file_put_contents('./discount_notify.log', $e->getMessage(), FILE_APPEND);
}
?>