
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';
require_once __DIR__ . '/../../tool/php/random_generator.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'])) {
            try {
                  $email = sanitize(rawurldecode($_POST['email']));

                  // Validate email
                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  if ($_SESSION['recovery_email'] !== $email) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Email not found!']);
                        exit;
                  }

                  $code = generateRandomString();
                  recovery_mail($email, $code);
                  if (!session_start())
                        throw new Exception('Error occurred during starting session!');
                  $_SESSION['recovery_code'] = $code;
                  $_SESSION['recovery_code_send_time'] = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                  echo json_encode(['query_result' => true]);
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