
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
            file_put_contents(__DIR__ . '\delete_account.log', $logMessage, FILE_APPEND);
            exit;
      }

      $stmt = $conn->prepare("SELECT customer.id,email,imagePath from customer join appUser on appUser.id=customer.id where status=false and deleteTime is not null and deleteTime<=now() and email is not null and phone is not null");
      if (!$stmt) {
            $logMessage = $currentDateTime . " - MySQL Query `SELECT customer.id,email,imagePath from customer join appUser on appUser.id=customer.id where status=false and deleteTime is not null and deleteTime<=now() and email is not null and phone is not null` Preparation Failed!\n";
            file_put_contents(__DIR__ . '\delete_account.log', $logMessage, FILE_APPEND);
            exit;
      }
      $isSuccess = $stmt->execute();
      if (!$isSuccess) {
            $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
            file_put_contents(__DIR__ . '\delete_account.log', $logMessage, FILE_APPEND);
            exit;
      }
      $result = $stmt->get_result();
      $total = $result->num_rows;
      $stmt2 = $conn->prepare("update appUser join customer on appUser.id = customer.id set email=null,phone=null,deleteTime=null,cardNumber=null,referrer=null,imagePath=null,address=null,password=null,name='ACCOUNT REMOVED' where customer.id=?");
      if (!$stmt2) {
            $logMessage = $currentDateTime . " - MySQL Query `update appUser join customer on appUser.id = customer.id set email=null,phone=null,deleteTime=null,cardNumber=null,referrer=null,imagePath=null,address=null,password=null,name='ACCOUNT REMOVED' where customer.id=?` Preparation Failed!\n";
            file_put_contents(__DIR__ . '\delete_account.log', $logMessage, FILE_APPEND);
            exit;
      }
      while ($row = $result->fetch_assoc()) {
            // Send delete mail
            delete_mail($row['email'], 2);

            // Delete image directory
            if ($row['imagePath'])
                  rrmdir(dirname(__DIR__ . '/../../../data/user/customer/' . $row['imagePath']));

            $stmt2->bind_param("s", $row['id']);
            $isSuccess = $stmt2->execute();
            if (!$isSuccess) {
                  $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt2->error}\n";
                  file_put_contents(__DIR__ . '\delete_account.log', $logMessage, FILE_APPEND);
                  exit;
            }
      }
      $stmt2->close();
      $stmt->close();
      $conn->close();

      file_put_contents(__DIR__ . '\delete_account.log', $currentDateTime . " - Task terminated, {$total} account(s) deleted!\n", FILE_APPEND);
} catch (Exception $e) {
      file_put_contents('./delete_account.log', $e->getMessage(), FILE_APPEND);
}

?>