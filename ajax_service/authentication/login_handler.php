
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/password.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['email'], $_POST['password'], $_POST['type'])) {
            try {
                  $email = sanitize($_POST['email']);
                  $password = sanitize($_POST['password']);
                  $user_type = sanitize($_POST['type']);

                  // Validate email
                  if (!$email) {
                        echo json_encode(['error' => 'No email address provided!']);
                        exit;
                  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode(['error' => 'Invalid email format!']);
                        exit;
                  }

                  // Validate password
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

                  // Valid user type
                  if (!$user_type) {
                        echo json_encode(['error' => 'No user type provided!']);
                        exit;
                  } else if ($user_type !== 'admin' && $user_type !== 'customer') {
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
                  $stmt = NULL;
                  if ($user_type === "admin") {
                        $stmt = $conn->prepare("select appUser.id,appUser.password from appUser join admin on admin.id=appUser.id where appUser.email=?");
                        $stmt->bind_param('s', $email);
                        $stmt->execute();
                  } else if ($user_type === "customer") {
                        $stmt = $conn->prepare("select appUser.id,appUser.password from appUser join customer on customer.id=appUser.id where appUser.email=?");
                        $stmt->bind_param('s', $email);
                        $stmt->execute();
                  }
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  echo $result['password'];
                  if (!verify_password($password, $result['password']))
                        echo json_encode(['error' => 'Email or password incorrect!']);
                  else {
                        // Start or resume the session
                        session_start();
                        $_SESSION['type'] = $user_type;
                        $_SESSION['id'] = $result['id'];

                        echo json_encode(['query_result' => true]);

                        // Missing a procedure to set status=1 when status=0 and send an email to the customer
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