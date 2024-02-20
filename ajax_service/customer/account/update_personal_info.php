
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session()) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
} else if ($_SESSION['type'] !== 'customer') {
      http_response_code(400);
      echo json_encode(['error' => 'Bad request!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
require_once __DIR__ . '/../../../tool/php/checker.php';
require_once __DIR__ . '/../../../tool/php/send_mail.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (
            isset($_POST['name']) &&
            isset($_POST['phone']) &&
            isset($_POST['dob']) &&
            isset($_POST['gender']) &&
            isset($_POST['address'])
      ) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $name = sanitize(rawurldecode($_POST['name']));
                  $phone = sanitize(rawurldecode($_POST['phone']));
                  $dob = sanitize(rawurldecode($_POST['dob']));
                  $gender = sanitize(rawurldecode($_POST['gender']));
                  $address = sanitize(rawurldecode($_POST['address']));

                  if (isset($_FILES['image'])) {
                        if (is_array($_FILES['image']['name'])) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Only 1 submitted image file allowed!']);
                              exit;
                        }

                        $allowedImageTypes = ['image/jpeg', 'image/png'];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo === false) {
                              throw new Exception("Failed to open fileinfo database!");
                        }
                        $fileMimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                        if ($fileMimeType === false) {
                              throw new Exception("Failed to get the MIME type of the image file!");
                        }
                        $finfoCloseResult = finfo_close($finfo);
                        if (!$finfoCloseResult) {
                              throw new Exception("Failed to close fileinfo resource!");
                        }

                        if (!in_array($fileMimeType, $allowedImageTypes)) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Invalid image file!']);
                              exit;
                        } else if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Image size must be 5MB or less!']);
                              exit;
                        }
                  }

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No name provided!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Name must be 255 characters long or less!']);
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

                  if (!$address) {
                        http_response_code(400);
                        echo json_encode(['error' => "No address provided!"]);
                        exit;
                  } else if (strlen($address) > 1000) {
                        http_response_code(400);
                        echo json_encode(['error' => "Address must be 1000 characters long or less!"]);
                        exit;
                  }

                  if (!$dob) {
                        http_response_code(400);
                        echo json_encode(['error' => 'No date of birth provided!']);
                        exit;
                  } else {
                        // Create a DateTime object for the date of birth
                        $dobDate = new DateTime($dob, new DateTimeZone($_ENV['TIMEZONE']));
                        $dobDate->setTime(0, 0, 0); // Set time to 00:00:00

                        // Get the current date
                        $currentDate = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                        $currentDate->setTime(0, 0, 0); // Set time to 00:00:00

                        if ($dobDate > $currentDate) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Date of birth invalid!']);
                              exit;
                        } else if (!isAgeValid($dob)) {

                              http_response_code(400);
                              echo json_encode(['error' => 'You must be at least 18 years old!']);
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

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $conn->begin_transaction();

                  $stmt = $conn->prepare("UPDATE appUser JOIN customer ON appUser.id = customer.id SET name=?, phone=?, address=?, dob=?, gender=? WHERE customer.id=?");
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `UPDATE appUser JOIN customer ON appUser.id = customer.id SET name=?, phone=?, address=?, dob=?, gender=? WHERE customer.id=?` preparation failed!']);
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ssssss', $name, $phone, $address, $dob, $gender, $_SESSION['id']);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  if (isset($_FILES['image'])) {
                        $imageFile = null;

                        $stmt = $conn->prepare('select imagePath from appUser join customer on customer.id=appUser.id where customer.id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select imagePath from appUser join customer on customer.id=appUser.id where customer.id=?` preparation failed!']);
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $_SESSION['id']);
                        $isSuccess = $stmt->execute();
                        if (!$isSuccess) {
                              http_response_code(500);
                              echo json_encode(['error' => $stmt->error]);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        } else {
                              $result = $stmt->get_result();
                              $result = $result->fetch_assoc();

                              $stmt->close();

                              $currentDateTime = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                              $currentDateTime = $currentDateTime->format('YmdHis');
                              $fileExtension = $_FILES['image']['type'] === 'image/png' ? 'png' : 'jpeg';
                              $imageDir = null;

                              if ($result['imagePath']) {
                                    $path = dirname(dirname(dirname(__DIR__))) . "/data/user/customer/" . $result['imagePath'];
                                    $temp_arr = explode('/', $result['imagePath']);
                                    array_pop($temp_arr);
                                    $imageDir = implode('/', $temp_arr);
                                    $imageFile = implode('/', $temp_arr) . "/{$_SESSION['id']}-{$currentDateTime}.{$fileExtension}";
                              } else {
                                    $imageDir = $_SESSION['id'];
                                    $imageFile = "{$_SESSION['id']}/{$_SESSION['id']}-{$currentDateTime}.{$fileExtension}";
                              }

                              if (!is_dir(dirname(dirname(dirname(__DIR__))) . "/data/user/customer/" . $imageDir)) {
                                    if (!mkdir(dirname(dirname(dirname(__DIR__))) . "/data/user/customer/" . $imageDir)) {
                                          $conn->rollback();
                                          $conn->close();
                                          throw new Exception("Error occurred during creating directory!");
                                    }
                              }

                              if (!move_uploaded_file($_FILES["image"]["tmp_name"], dirname(dirname(dirname(__DIR__))) . "/data/user/customer/" . $imageFile)) {
                                    $conn->rollback();
                                    $conn->close();
                                    throw new Exception("Error occurred during moving image file!");
                              }

                              $stmt = $conn->prepare('update appUser join customer on customer.id=appUser.id set imagePath=? where customer.id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `update appUser join customer on customer.id=appUser.id set imagePath=? where customer.id=?` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $imageFile, $_SESSION['id']);
                              $isSuccess = $stmt->execute();

                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->rollback();
                                    $conn->close();
                                    exit;
                              } else {
                                    if ($stmt->affected_rows > 1) {
                                          http_response_code(500);
                                          echo json_encode(['error' => 'Updated more than one customer account!']);
                                          $stmt->close();
                                          $conn->rollback();
                                          $conn->close();
                                          exit;
                                    }
                                    $stmt->close();
                              }
                        }
                  }

                  $conn->commit();

                  $stmt = $conn->prepare('select email from appUser join customer on customer.id=appUser.id where customer.id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select email from appUser join customer on customer.id=appUser.id where customer.id=?` preparation failed!']);
                        exit;
                  }
                  $stmt->bind_param('s', $_SESSION['id']);
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
                  $email = $result['email'];
                  $stmt->close();
                  $conn->close();
                  personal_info_change($email, 'customer');
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