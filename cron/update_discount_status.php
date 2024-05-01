
<?php
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../tool/php/send_mail.php';
require_once __DIR__ . '/../tool/php/delete_directory.php';

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
      $currentDateTime = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
      $currentDateTime = $currentDateTime->format('Y-m-d H:i:s');

      $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

      if (!$conn) {
            $logMessage = $currentDateTime . " - MySQL Connection Failed!\n";
            file_put_contents(__DIR__ . '/update_discount_status.log', $logMessage, FILE_APPEND);
            exit;
      }

      $total = 0;

      $stmt = $conn->prepare("select count(*) as counter from eventDiscount where endDate<curdate();");
      if (!$stmt) {
            $logMessage = $currentDateTime . " - MySQL Query `select count(*) as counter from eventDiscount where endDate<curdate();` Preparation Failed!\n";
            file_put_contents(__DIR__ . '/update_discount_status.log', $logMessage, FILE_APPEND);
            exit;
      }
      $isSuccess = $stmt->execute();
      if (!$isSuccess) {
            $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
            file_put_contents(__DIR__ . '/update_discount_status.log', $logMessage, FILE_APPEND);
            exit;
      }
      $total = $stmt->get_result()->fetch_assoc()['counter'];
      $stmt->close();

      $stmt = $conn->prepare('update discount set status=false where id in(select id from eventDiscount where endDate<curdate());');
      if (!$stmt) {
            $logMessage = $currentDateTime . " - MySQL Query `update discount set status=false where id in(select id from eventDiscount where endDate<curdate());` Preparation Failed!\n";
            file_put_contents(__DIR__ . '/update_discount_status.log', $logMessage, FILE_APPEND);
            exit;
      }
      $isSuccess = $stmt->execute();
      if (!$isSuccess) {
            $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
            file_put_contents(__DIR__ . '/update_discount_status.log', $logMessage, FILE_APPEND);
            exit;
      }
      $stmt->close();

      $conn->close();

      file_put_contents(__DIR__ . '/update_discount_status.log', $currentDateTime . " - Task terminated, {$total} discount event(s) updated!\n", FILE_APPEND);
} catch (Exception $e) {
      file_put_contents(__DIR__ . '/delete_account.log', $e->getMessage(), FILE_APPEND);
}

?>