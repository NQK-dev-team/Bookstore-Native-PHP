
<?php
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../tool/php/send_mail.php';
require_once __DIR__ . '/../tool/php/converter.php';

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
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }

      $stmt = $conn->prepare("select discount.id,discount,name,applyForAll from eventDiscount join discount on discount.id=eventDiscount.id where status=true and startDate<=curdate() and not isNotify");
      if (!$stmt) {
            $logMessage = $currentDateTime . " - MySQL Query `select discount.id,discount,name,applyForAll from eventDiscount join discount on discount.id=eventDiscount.id where status=true and startDate<=curdate() and not isNotify` Preparation Failed!\n";
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }
      $isSuccess = $stmt->execute();
      if (!$isSuccess) {
            $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }
      $result = $stmt->get_result();
      $totalEvent = $result->num_rows;
      $result = $result->fetch_all(MYSQLI_ASSOC);

      $stmt2 = $conn->prepare('select email from appUser join customer on customer.id=appUser.id where status=true and email is not null and phone is not null and deleteTime is null');
      if (!$stmt2) {
            $logMessage = $currentDateTime . " - MySQL Query `select email from appUser join customer on customer.id=appUser.id where status=true and email is not null and phone is not null and deleteTime is null` Preparation Failed!\n";
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }
      $isSuccess = $stmt2->execute();
      if (!$isSuccess) {
            $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt2->error}\n";
            file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
            exit;
      }
      $result2 = $stmt2->get_result();
      $totalCustomer = $result2->num_rows;
      $result2 = $result2->fetch_all(MYSQLI_ASSOC);
      $stmt2->close();
      $stmt->close();

      foreach ($result as $discount) {
            $books = [];
            if (!$discount['applyForAll']) {
                  $stmt = $conn->prepare('select name,edition from book join eventApply on eventApply.bookID=book.id where eventApply.eventID=?');
                  if (!$stmt) {
                        $logMessage = $currentDateTime . " - MySQL Query `select name,edition from book join eventApply on eventApply.bookID=book.id where eventApply.eventID=?` Preparation Failed!\n";
                        file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
                        exit;
                  }
                  $stmt->bind_param('s', $discount['id']);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
                        file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
                        exit;
                  }
                  $result3 = $stmt->get_result();
                  while ($row = $result3->fetch_assoc()) {
                        $books[] = $row['name'] . ' - ' . convertToOrdinal($row['edition']) .' edition';
                  }
                  $stmt->close();
            }
            foreach ($result2 as $customer) {
                  discount_notify($customer['email'], $discount['name'], $discount['discount'], $discount['applyForAll'] ? 1 : 2, $books);
            }

            $stmt = $conn->prepare('update eventDiscount set isNotify=true where id=?');
            if (!$stmt) {
                  $logMessage = $currentDateTime . " - MySQL Query `update eventDiscount set isNotify=true where id=?` Preparation Failed!\n";
                  file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
                  exit;
            }
            $stmt->bind_param('s', $discount['id']);
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  $logMessage = $currentDateTime . " - MySQL Query Error: {$stmt->error}\n";
                  file_put_contents(__DIR__ . '\discount_notify.log', $logMessage, FILE_APPEND);
                  exit;
            }
            $stmt->close();
      }

      $conn->close();

      file_put_contents(__DIR__ . '\discount_notify.log', $currentDateTime . " - Task terminated, {$totalEvent} event(s) notified to {$totalCustomer} customer(s)!\n", FILE_APPEND);
} catch (Exception $e) {
      file_put_contents('./discount_notify.log', $e->getMessage(), FILE_APPEND);
}
?>