
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';
require_once __DIR__ . '/../../tool/php/send_mail.php';

function isAgeValid($input)
{
      // Assuming $input is the date of birth in 'Y-m-d' format
      $dob = new DateTime($input, new DateTimeZone('Asia/Ho_Chi_Minh'));
      $today = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
      $age = $today->format('Y') - $dob->format('Y');

      // Check if the birthday has occurred this year
      if ($today->format('m') < $dob->format('m') || ($today->format('m') == $dob->format('m') && $today->format('d') < $dob->format('d'))) {
            $age--;
      }

      return $age >= 18;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'], $_POST['name'], $_POST['date'], $_POST['phone'], $_POST['address'])) {
            try {
                  $email = sanitize($_POST['email']);
                  $password = sanitize($_POST['password']);
                  $name = sanitize($_POST['name']);
                  $date = sanitize($_POST['date']);
                  $phone = sanitize($_POST['phone']);
                  $address = $_POST['address'] ? sanitize($_POST['address']) : null;
                  $card = $_POST['card'] ? sanitize($_POST['card']) : null;
                  $refEmail = $_POST['refEmail'] ? sanitize($_POST['refEmail']) : null;

                  if (!$name) {
                        echo json_encode(['error' => 'No name provided!']);
                        exit;
                  }

                  if (!$date) {
                        echo json_encode(['error' => 'No date of birth provided!']);
                        exit;
                  } else {
                        // Create a DateTime object for the date of birth
                        $dobDate = new DateTime($date, new DateTimeZone('Asia/Ho_Chi_Minh'));

                        // Get the current date
                        $currentDate = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));

                        if ($dobDate > $currentDate) {
                              echo json_encode(['error' => 'Date of birth invalid!']);
                              exit;
                        } else if (!isAgeValid($date)) {

                              echo json_encode(['error' => 'You must be at least 18 years old to sign up!']);
                              exit;
                        }
                  }

                  if (!$phone) {
                        echo json_encode(['error' => 'No phone number provided!']);
                        exit;
                  } else if (!preg_match('/^[0-9]{10}$/', $phone)) {
                        echo json_encode(['error' => 'Invalid phone number format!']);
                        exit;
                  }

                  if ($card && !preg_match('/^[0-9]{8,16}$/', $card)) {
                        echo json_encode(['error' => 'Card number format invalid!']);
                        exit;
                  }

                  if (!$email) {
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  if (!$password) {
                        echo json_encode(['error' => 'No password provided!']);
                        exit;
                  } else if (strlen($password) < 8) {
                        echo json_encode(['error' => 'Password must be at least 8 characters!']);
                        exit;
                  } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $password)) {
                        echo json_encode(['error' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!']);
                        exit;
                  }

                  if ($refEmail && !filter_var($refEmail, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode(['error' => 'Invalid referrer email format!']);
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
                  $stmt->bind_param('s', $phone);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  if ($result['result'] === 1) {
                        echo json_encode(['error' => 'Phone number has been used!']);
                        $stmt->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select exists(select * from appUser where email=?) as result');
                  $stmt->bind_param('s', $email);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  if ($result['result'] === 1) {
                        echo json_encode(['error' => 'Email has been used!']);
                        $stmt->close();
                        exit;
                  }
                  $stmt->close();

                  if ($refEmail) {
                        $stmt = $conn->prepare('select exists(select * from appUser where email=?) as result');
                        $stmt->bind_param('s', $refEmail);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        if ($result['result'] === 0) {
                              echo json_encode(['error' => 'Referrer email not found!']);
                              $stmt->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $hashedPassword = hash_password($password);
                  $stmt = $conn->prepare('call addCustomer(?,?,?,?,?,?,?,?)');
                  $stmt->bind_param('ssssssss', $name, $date, $phone, $address, $card, $email, $hashedPassword, $refEmail);
                  $stmt->execute();

                  if ($stmt->affected_rows <= 0) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        echo json_encode(['query_result' => true]);
                        create_new_account_mail($email);
                        if ($refEmail)
                              referrer_mail($refEmail, $email);
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