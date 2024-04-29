
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';
require_once __DIR__ . '/../../tool/php/check_https.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['type'])) {
            try {
                  if (!session_set_cookie_params([
                        'lifetime' => 3 * 24 * 60 * 60,
                        'path' => '/',
                        'domain' => '',
                        'secure' => isSecure(),
                        'httponly' => true,
                        'samesite' => 'Strict'
                  ])) throw new Exception('Error occurred during setting up session attributes!');

                  if (!session_start())
                        throw new Exception('Error occurred during starting session!');

                  if (isset($_SESSION['login_cool_down']) && $_SESSION['login_cool_down'] < time()) {
                        $_SESSION['login_try'] = 0;
                        unset($_SESSION['login_cool_down']);
                  }

                  if (!isset($_SESSION['login_try'])) $_SESSION['login_try'] = 0;
                  else if ($_SESSION['login_try'] >= 5) {
                        http_response_code(429);
                        if (!isset($_SESSION['login_cool_down'])) {
                              $_SESSION['login_cool_down'] = time() + 300;
                              echo json_encode(['error' => 'Too many login attempts, try again after 5 minutes!']);
                        } else {
                              $remainingSeconds = $_SESSION['login_cool_down'] - time();
                              $minutes = floor($remainingSeconds / 60);
                              $seconds = $remainingSeconds % 60;

                              if ($minutes > 0) {
                                    echo json_encode(['error' => 'Too many login attempts, try again after ' . $minutes . ' minutes and ' . $seconds . ' seconds!']);
                              } else {
                                    echo json_encode(['error' => 'Too many login attempts, try again after ' . $seconds . ' seconds!']);
                              }
                        }
                        exit;
                  }


                  $email = sanitize(rawurldecode($_POST['email']));
                  $password = sanitize(rawurldecode($_POST['password']));
                  $user_type = sanitize(rawurldecode($_POST['type']));

                  // Validate email
                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  }

                  // Validate password
                  if (!$password) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No password provided!']);
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
                  $id = null;
                  $stmt = NULL;
                  if ($user_type === "admin") {
                        $stmt = $conn->prepare("select appUser.id,appUser.password from appUser join admin on admin.id=appUser.id where appUser.email=?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select appUser.id,appUser.password from appUser join admin on admin.id=appUser.id where appUser.email=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $email);
                        $isSuccess = $stmt->execute();
                  } else if ($user_type === "customer") {
                        $stmt = $conn->prepare("select appUser.id,appUser.password from appUser join customer on customer.id=appUser.id where appUser.email=?");
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select appUser.id,appUser.password from appUser join customer on customer.id=appUser.id where appUser.email=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $email);
                        $isSuccess = $stmt->execute();
                  }
                  if ($isSuccess) {
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              echo json_encode(['error' => 'Email or password incorrect!']);
                              $_SESSION['login_try']++;
                              $stmt->close();
                              $conn->close();
                              exit;
                        } else {
                              $result = $result->fetch_assoc();
                              if (!verify_password($password, $result['password'])) {
                                    echo json_encode(['error' => 'Email or password incorrect!']);
                                    $_SESSION['login_try']++;
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              } else {
                                    $id = $result['id'];
                              }
                        }
                  } else {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  // Close statement
                  $stmt->close();

                  if ($user_type === "customer") {
                        $stmt = $conn->prepare('select status,deleteTime from customer join appUser on appUser.id=customer.id where customer.id=? and email is not null and phone is not null');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select status,deleteTime from customer join appUser on appUser.id=customer.id where customer.id=? and email is not null and phone is not null` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                              echo json_encode(['error' => 'This account has been deleted just now!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $result->fetch_assoc();
                        $customerStatus = $result['status'];
                        $customerDeleteTime = $result['deleteTime'];
                        $stmt->close();

                        if (!$customerStatus) {
                              $stmt = $conn->prepare('update customer join appUser on appUser.id=customer.id set status=true,deleteTime=null where customer.id=? and email is not null and phone is not null');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `update customer join appUser on appUser.id=customer.id set status=true,deleteTime=null where customer.id=? and email is not null and phone is not null` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('s', $id);
                              $isSuccess = $stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              } else if ($stmt->affected_rows === 0) {
                                    echo json_encode(['error' => 'This account has been deleted just now!']);
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();

                              if ($customerDeleteTime)
                                    delete_cancel_mail($email);
                              else
                                    activate_mail($email);
                        }
                  }

                  $_SESSION['type'] = $user_type;
                  $_SESSION['id'] = $id;
                  generateToken();

                  echo json_encode(['query_result' => true]);
                  $_SESSION['login_try'] = 0;

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