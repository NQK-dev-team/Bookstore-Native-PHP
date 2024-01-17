
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['code'])) {
            try {
                  $email = sanitize(rawurldecode($_POST['email']));
                  $code = sanitize(rawurldecode($_POST['code']));

                  // Validate email
                  if (!$email) {
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  // Validate code
                  if (!$code) {
                        echo json_encode(['error' => 'No recovery code provided!']);
                        exit;
                  } else if (!preg_match('/^[0-9a-zA-Z]{8}$/', $code)) {
                        echo json_encode(['error' => 'Invalid recovery code format!']);
                        exit;
                  }
                  session_start();
                  if (isset($_SESSION['recovery_code'], $_SESSION['recovery_code_send_time'], $_SESSION['recovery_email']) && $_SESSION['recovery_code'] && $_SESSION['recovery_code_send_time'] && $_SESSION['recovery_email']) {
                        if ($email === $_SESSION['recovery_email']) {
                              if ($code === $_SESSION['recovery_code']) {
                                    $current_time = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                                    $interval = $current_time->getTimestamp() - $_SESSION['recovery_code_send_time']->getTimestamp();
                                    if (abs($interval) <= 120) {
                                          echo json_encode(['query_result' => true]);
                                          $_SESSION['recovery_state'] = true;
                                          $_SESSION['recovery_state_set_time'] = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                                    } else {
                                          echo json_encode(['error' => 'Recovery code expired!']);
                                    }
                              } else {
                                    echo json_encode(['error' => 'Recovery code incorrect!']);
                              }
                        } else {
                              echo json_encode(['error' => 'Recovery email not matched!']);
                        }
                  } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Server can\'t find information about client\'s recovery request!']);
                  }
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