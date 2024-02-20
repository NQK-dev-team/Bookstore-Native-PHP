
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';
require_once __DIR__ . '/../../tool/php/random_generator.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email']) && isset($_POST['type'])) {
            try {
                  $email = sanitize(rawurldecode($_POST['email']));
                  $user_type = sanitize(rawurldecode($_POST['type']));

                  // Validate email
                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  }

                  // Valid user type
                  if (!$user_type) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No user type provided!']);
                        exit;
                  } else if ($user_type !== 'admin' && $user_type !== 'customer') {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid user type!']);
                        exit;
                  }

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  // Using prepare statement (preventing SQL injection)
                  if ($user_type === "admin") {
                        $stmt = $conn->prepare("select appUser.id from appUser join admin on admin.id=appUser.id where appUser.email=?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select appUser.id from appUser join admin on admin.id=appUser.id where appUser.email=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $email);
                        $isSuccess = $stmt->execute();
                  } else if ($user_type === "customer") {
                        $stmt = $conn->prepare("select appUser.id from appUser join customer on customer.id=appUser.id where appUser.email=?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select appUser.id from appUser join customer on customer.id=appUser.id where appUser.email=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $email);
                        $isSuccess = $stmt->execute();
                  }
                  if ($isSuccess) {
                        $result = $stmt->get_result();
                        $result = $result->num_rows;
                        if ($result === 0) {
                              http_response_code(404);
                              echo json_encode(['error' => 'Email not found!']);
                        } else if ($result === 1) {
                              echo json_encode(['query_result' => true]);
                              $code = generateRandomString();
                              recovery_mail($email, $code);
                              if (!session_start())
                                    throw new Exception('Error occurred during starting session!');
                              $_SESSION['recovery_code'] = $code;
                              $_SESSION['recovery_code_send_time'] = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                              $_SESSION['recovery_email'] = $email;
                        }
                  } else {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  }
                  // Close statement
                  $stmt->close();

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