
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';
require_once __DIR__ . '/../../tool/php/checker.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (
            isset($_POST['email']) &&
            isset($_POST['password']) &&
            isset($_POST['name']) &&
            isset($_POST['date']) &&
            isset($_POST['phone']) &&
            isset($_POST['gender']) &&
            isset($_POST['confirmPassword'])
      ) {
            try {
                  $email = sanitize(rawurldecode($_POST['email']));
                  $password = sanitize(rawurldecode($_POST['password']));
                  $name = sanitize(rawurldecode($_POST['name']));
                  $date = sanitize(rawurldecode($_POST['date']));
                  $phone = sanitize(rawurldecode($_POST['phone']));
                  $address = (isset($_POST['address']) && $_POST['address']) ? sanitize(rawurldecode($_POST['address'])) : null;
                  $card = (isset($_POST['card']) && $_POST['card']) ? sanitize(rawurldecode($_POST['card'])) : null;
                  $refEmail = (isset($_POST['refEmail']) && $_POST['refEmail']) ? sanitize(rawurldecode($_POST['refEmail'])) : null;
                  $gender = sanitize(rawurldecode($_POST['gender']));
                  $confirmPassword = sanitize(rawurldecode($_POST['confirmPassword']));

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No name provided!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Name must be 255 characters long or less!']);
                        exit;
                  }

                  if (!$date) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No date of birth provided!']);
                        exit;
                  } else {
                        // Create a DateTime object for the date of birth
                        $dobDate = new DateTime($date, new DateTimeZone($_ENV['TIMEZONE']));
                        $dobDate->setTime(0, 0, 0); // Set time to 00:00:00

                        // Get the current date
                        $currentDate = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                        $currentDate->setTime(0, 0, 0); // Set time to 00:00:00

                        if ($dobDate > $currentDate) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Date of birth invalid!']);
                              exit;
                        } else if (!isAgeValid($date)) {

                              http_response_code(400);
                              echo json_encode(['error' => 'You must be at least 18 years old to sign up!']);
                              exit;
                        }
                  }

                  if (!$gender || $gender === 'null') {
                        http_response_code(400);
                        echo json_encode(['error' => 'No gender provided!']);
                        exit;
                  } else if ($gender !== 'M' && $gender !== 'F' && $gender !== 'O') {
                        http_response_code(400);
                        echo json_encode(['error' => 'Gender invalid!']);
                        exit;
                  }

                  if (!$phone) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No phone number provided!']);
                        exit;
                  } else {
                        $matchResult = preg_match('/^[0-9]{10}$/', $phone);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during phone number format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Invalid phone number format!']);
                              exit;
                        }
                  }

                  if ($card) {
                        $matchResult = preg_match('/^[0-9]{8,16}$/', $card);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during card number format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Card number format invalid!']);
                              exit;
                        }
                  }

                  if (!$email) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  } else if (strlen($email) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Email must be 255 characters long or less!']);
                        exit;
                  }

                  if (!$password) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No password provided!']);
                        exit;
                  } else if (strlen($password) < 8) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Password must be at least 8 characters long!']);
                        exit;
                  } else {
                        $matchResult = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,72}$/', $password);
                        if ($matchResult === false) {
                              throw new Exception('Error occurred during password format check!');
                        } else if ($matchResult === 0) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters!']);
                              exit;
                        }
                  }

                  if (!$confirmPassword) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Confirm password not provided!']);
                        exit;
                  } else if ($confirmPassword !== $password) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Confirm password does not match!']);
                        exit;
                  }

                  if ($refEmail && !filter_var($refEmail, FILTER_VALIDATE_EMAIL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid referrer email format!']);
                        exit;
                  } else if (strlen($refEmail) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Refferer email must be 255 characters long or less!']);
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
                  $stmt = $conn->prepare('select exists(select * from appUser where phone=?) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from appUser where phone=?) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $phone);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  if ($result['result'] === 1) {
                        echo json_encode(['error' => 'Phone number has been used!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from appUser where email=?) as result');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select exists(select * from appUser where email=?) as result` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $email);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  if ($result['result'] === 1) {
                        echo json_encode(['error' => 'Email has been used!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  if ($refEmail) {
                        $stmt = $conn->prepare('select exists(select * from appUser where email=?) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from appUser where email=?) as result` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $refEmail);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        if ($result['result'] === 0) {
                              echo json_encode(['error' => 'Referrer email not found!']);
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $hashedPassword = hash_password($password);
                  if ($hashedPassword === false) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Password hashing failed!']);
                        $conn->close();
                        exit;
                  } else if (is_null($hashedPassword)) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Password hashing algorithm invalid!']);
                        $conn->close();
                        exit;
                  }

                  // Begin transaction
                  $conn->begin_transaction();

                  $stmt = $conn->prepare('call addCustomer(?,?,?,?,?,?,?,?,?)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `call addCustomer(?,?,?,?,?,?,?,?,?)` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sssssssss', $name, $date, $phone, $address, $card, $email, $hashedPassword, $refEmail, $gender);
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $conn->rollback();
                  } else {
                        if ($stmt->affected_rows === 0) {
                              echo json_encode(['query_result' => false]);
                        } else {
                              create_new_account_mail($email);
                              if ($refEmail)
                                    referrer_mail($refEmail, $email);
                              echo json_encode(['query_result' => true]);
                        }
                  }

                  // Close statement
                  $stmt->close();

                  // Commit transaction
                  $conn->commit();

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