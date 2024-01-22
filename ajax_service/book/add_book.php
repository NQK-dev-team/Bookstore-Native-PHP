
<?php
require_once __DIR__ . '/../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../tool/php/anti_csrf.php';

function map($elem)
{
      return sanitize(rawurldecode($elem));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset(
            $_POST['name'],
            $_POST['edition'],
            $_POST['isbn'],
            $_POST['age'],
            $_POST['author'],
            $_POST['category'],
            $_POST['publisher'],
            $_POST['publishDate'],
            $_POST['description'],
            $_POST['physicalPrice'],
            $_POST['filePrice'],
            $_POST['inStock'],
            $_POST['csrf_token']
      )) {
            try {
                  if (!checkToken($_POST['csrf_token'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $name = sanitize(rawurldecode($_POST['name']));
                  $edition = sanitize(rawurldecode($_POST['edition']));
                  $isbn = sanitize(str_replace('-', '', rawurldecode($_POST['isbn'])));
                  $age = sanitize(rawurldecode($_POST['age'])) ? sanitize(rawurldecode($_POST['age'])) : null;
                  $author =  $_POST['author'] ? array_map('map', explode(',', $_POST['author'])) : [];
                  $category = $_POST['category'] ? array_map('map', explode(',', $_POST['category'])) : [];
                  $publisher = sanitize(rawurldecode($_POST['publisher']));
                  $publishDate = sanitize(rawurldecode($_POST['publishDate']));
                  $description = sanitize(rawurldecode($_POST['description'])) ? sanitize(rawurldecode($_POST['description'])) : null;
                  $physicalPrice = sanitize(rawurldecode($_POST['physicalPrice'])) ? sanitize(rawurldecode($_POST['physicalPrice'])) : null;
                  $filePrice = sanitize(rawurldecode($_POST['filePrice'])) ? sanitize(rawurldecode($_POST['filePrice'])) : null;
                  $inStock = sanitize(rawurldecode($_POST['inStock'])) ? sanitize(rawurldecode($_POST['inStock'])) : null;

                  if (!$name) {
                        echo json_encode(['error' => 'Book name is empty!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name) === 0) {
                        echo json_encode(['error' => 'Book name must not contain \'?\', \'/\' or \'\\\' characters!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        echo json_encode(['error' => 'Book name must be 255 characters long or less!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name) === false) {
                        throw new Exception('Error occurred during book name format check!');
                        exit;
                  }

                  if (!$edition) {
                        echo json_encode(['error' => 'Book edition is empty!']);
                        exit;
                  } else if (!is_numeric($edition) || $edition <= 0) {
                        echo json_encode(['error' => 'Book edition invalid!']);
                        exit;
                  }

                  if (!$isbn) {
                        echo json_encode(['error' => 'Book ISBN-13 is empty!']);
                        exit;
                  } else if (preg_match('/^[0-9]{13}$/', $isbn) === 0) {
                        echo json_encode(['error' => 'Book ISBN-13 invalid!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name) === false) {
                        throw new Exception('Error occurred during book ISBN-13 format check!');
                        exit;
                  }

                  if ($age && (!is_numeric($age) || $age <= 0)) {
                        echo json_encode(['error' => 'Age restriction invalid!']);
                        exit;
                  }

                  if (!count($author)) {
                        echo json_encode(['error' => 'Book must have at least one author!']);
                        exit;
                  } else {
                        foreach ($author as $x) {
                              if (strlen($x) > 255) {
                                    echo json_encode(['error' => 'Author name must be 255 characters long or less!']);
                                    exit;
                              }
                        }
                  }

                  if (!$publisher) {
                        echo json_encode(['error' => 'Publisher is empty!']);
                        exit;
                  } else if (strlen($publisher) > 255) {
                        echo json_encode(['error' => 'Publisher must be 255 characters long or less!']);
                        exit;
                  }

                  if (!$publishDate) {
                        echo json_encode(['error' => 'Publish date is empty!']);
                        exit;
                  } else {
                        // Create a DateTime object for the date of birth
                        $tempDate = new DateTime($publishDate, new DateTimeZone('Asia/Ho_Chi_Minh'));

                        // Get the current date
                        $currentDate = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));

                        if ($tempDate > $currentDate) {
                              echo json_encode(['error' => 'Publish date invalid!']);
                              exit;
                        }
                  }

                  if ($description && strlen($description) > 2000) {
                        echo json_encode(['error' => 'Description must be 2000 characters long or less!']);
                        exit;
                  }

                  if ($physicalPrice && (!is_numeric($physicalPrice) || $physicalPrice <= 0)) {
                        echo json_encode(['error' => 'Physical copy price invalid!']);
                        exit;
                  }

                  if ($inStock && (!is_numeric($inStock) || $inStock < 0)) {
                        echo json_encode(['error' => 'Physical copy in stock invalid!']);
                        exit;
                  }

                  if ($filePrice && (!is_numeric($filePrice) || $filePrice <= 0)) {
                        echo json_encode(['error' => 'File copy price invalid!']);
                        exit;
                  }

                  $allowedImageTypes = ['image/jpeg', 'image/png'];
                  $allowedFileTypes = ['application/pdf'];

                  if (isset($_FILES['image'])) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo === false) {
                              throw new Exception("Failed to open fileinfo database!");
                        }
                        $fileMimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                        if ($fileMimeType === false) {
                              throw new Exception("Failed to get the MIME type of the image file!");
                        }
                        $finfoCloseResult = finfo_close($finfo);
                        if ($finfoCloseResult) {
                              throw new Exception("Failed to close fileinfo resource!");
                        }

                        if (!in_array($fileMimeType, $allowedImageTypes)) {
                              echo json_encode(['error' => 'Invalid image file!']);
                              exit;
                        } else if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                              echo json_encode(['error' => 'Image size must be 5MB or less!']);
                              exit;
                        }
                  } else {
                        echo json_encode(['error' => 'Missing image file!']);
                        exit;
                  }

                  if (isset($_FILES['pdf'])) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo === false) {
                              throw new Exception("Failed to open fileinfo database!");
                        }
                        $fileMimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
                        if ($fileMimeType === false) {
                              throw new Exception("Failed to get the MIME type of the PDF file!");
                        }
                        $finfoCloseResult = finfo_close($finfo);
                        if ($finfoCloseResult) {
                              throw new Exception("Failed to close fileinfo resource!");
                        }

                        if (!in_array($fileMimeType, $allowedFileTypes)) {
                              echo json_encode(['error' => 'Invalid PDF file!']);
                              exit;
                        }
                  }

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  // Check for existing book

                  $stmt = $conn->prepare('select * from book where name=? and edition=?');
                  $stmt->bind_param('si', $name, $edition);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 1) {
                        echo json_encode(['error' => 'Book name or edition found in another book!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select * from book where isbn=?');
                  $stmt->bind_param('s', $isbn);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 1) {
                        echo json_encode(['error' => 'Book ISBN-13 found in another book!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  // Start a transaction
                  $conn->begin_transaction();

                  $id = null;

                  $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
                  $currentDateTime = $currentDateTime->format('YmdHis');
                  $imageExtension = $_FILES['image']['type'] === 'image/png' ? 'png' : 'jpeg';
                  $imageFile = "{$name}-{$currentDateTime}.{$imageExtension}";

                  $pdfFile = isset($_FILES['pdf']) ? "{$name}-{$currentDateTime}.pdf" : null;

                  $stmt = $conn->prepare('call addBook(?,?,?,?,?,?,?,?,?,?,?,?)');
                  $stmt->bind_param('sisissssdids', $name, $edition, $isbn, $age, $publisher, $publishDate, $description, $imageFile, $physicalPrice, $inStock, $filePrice, $pdfFile);
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
                        if ($result->num_rows === 1) {
                              $result = $result->fetch_assoc();
                              $id = $result['id'];
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('insert into author(bookID,authorName) values(?,?)');
                  foreach ($author as $x) {
                        if ($x) {
                              $stmt->bind_param('ss', $id, $x);
                              $isSuccess = $stmt->execute();

                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->rollback();
                                    $conn->close();
                                    exit;
                              }
                        }
                  }
                  $stmt->close();

                  if (count($category)) {
                        $categoryID = [];
                        $stmt = $conn->prepare('select id from category where name=?');
                        foreach ($category as $x) {
                              if ($x) {
                                    $stmt->bind_param('s', $x);
                                    $isSuccess = $stmt->execute();
                                    $result = $stmt->get_result();

                                    if (!$isSuccess) {
                                          http_response_code(500);
                                          echo json_encode(['error' => $stmt->error]);
                                          $stmt->close();
                                          $conn->rollback();
                                          $conn->close();
                                          exit;
                                    } else {
                                          if ($result->num_rows === 0) {
                                                echo json_encode(['error' => "Category $x not found!"]);
                                                $stmt->close();
                                                $conn->rollback();
                                                $conn->close();
                                                exit;
                                          } else {
                                                $result = $result->fetch_assoc();
                                                $categoryID[] = $result['id'];
                                          }
                                    }
                              }
                        }
                        $stmt->close();

                        $stmt = $conn->prepare('insert into belong(bookID,categoryID) values(?,?)');
                        foreach ($categoryID as $x) {
                              $stmt->bind_param('ss', $id, $x);
                              $isSuccess = $stmt->execute();

                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $stmt->close();
                                    $conn->rollback();
                                    $conn->close();
                                    exit;
                              }
                        }
                        $stmt->close();
                  }

                  if (!is_dir(dirname(dirname(__DIR__)) . "/data/book/" . $id)) {
                        mkdir(dirname(dirname(__DIR__)) . "/data/book/" . $id);
                  } else {
                        throw new Exception("Error occurred during creating directory!");
                  }

                  if (!move_uploaded_file($_FILES["image"]["tmp_name"], dirname(dirname(__DIR__)) . "/data/book/{$id}/" . $imageFile))
                        throw new Exception("Error occurred during moving image file!");

                  if (isset($_FILES['pdf'])) {
                        if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], dirname(dirname(__DIR__)) . "/data/book/{$id}/" . $pdfFile))
                              throw new Exception("Error occurred during moving PDF file!");
                  }

                  echo json_encode(['query_result' => true]);

                  // Commit transaction
                  $conn->commit();

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