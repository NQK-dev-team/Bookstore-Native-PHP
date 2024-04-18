
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();


function map($elem)
{
      return sanitize(rawurldecode($elem));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (
            isset($_POST['name']) &&
            isset($_POST['edition']) &&
            isset($_POST['isbn']) &&
            isset($_POST['author']) &&
            isset($_POST['category']) &&
            isset($_POST['publisher']) &&
            isset($_POST['publishDate']) &&
            isset($_POST['description']) &&
            isset($_POST['physicalPrice']) &&
            isset($_POST['filePrice']) &&
            isset($_POST['inStock']) &&
            isset($_POST['removeFile'])
      ) {
            try {
                  if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !checkToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                        http_response_code(403);
                        echo json_encode(['error' => 'CSRF token validation failed!']);
                        exit;
                  }

                  $id = $_SESSION['update_book_id'];
                  $name = sanitize(rawurldecode($_POST['name']));
                  $edition = sanitize(rawurldecode($_POST['edition']));
                  $isbn = sanitize(str_replace('-', '', rawurldecode($_POST['isbn'])));
                  $author =  $_POST['author'] ? array_map('map', explode(',', $_POST['author'])) : [];
                  $category = $_POST['category'] ? array_map('map', explode("\n", rawurldecode($_POST['category']))) : [];
                  $publisher = sanitize(rawurldecode($_POST['publisher']));
                  $publishDate = sanitize(rawurldecode($_POST['publishDate']));
                  $description = sanitize(rawurldecode($_POST['description'])) ? sanitize(rawurldecode($_POST['description'])) : null;
                  $physicalPrice = sanitize(rawurldecode($_POST['physicalPrice'])) ? sanitize(rawurldecode($_POST['physicalPrice'])) : null;
                  $filePrice = sanitize(rawurldecode($_POST['filePrice'])) ? sanitize(rawurldecode($_POST['filePrice'])) : null;
                  $inStock = sanitize(rawurldecode($_POST['inStock'])) ? sanitize(rawurldecode($_POST['inStock'])) : 0;
                  $removeFile = filter_var(sanitize(rawurlencode($_POST['removeFile'])), FILTER_VALIDATE_BOOLEAN);

                  if (!$id) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Server can\'t find this book ID!']);
                        exit;
                  }

                  if (!$name) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book name is empty!']);
                        exit;
                  } else if (preg_match('/[?\/"]/', $name) === 1) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book name must not contain \'?\', \'/\', \'"\' or \'\\\' characters!']);
                        exit;
                  } else if (strlen($name) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book name must be at most 255 characters long or less!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name) === false) {
                        throw new Exception('Error occurred during book name format check!');
                        exit;
                  }

                  if (!$edition) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book edition is empty!']);
                        exit;
                  } else if (!is_numeric($edition) || $edition <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book edition invalid!']);
                        exit;
                  }

                  if (!$isbn) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book ISBN-13 is empty!']);
                        exit;
                  } else if (preg_match('/^[0-9]{13}$/', $isbn) === 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book ISBN-13 invalid!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name) === false) {
                        throw new Exception('Error occurred during book ISBN-13 format check!');
                        exit;
                  }

                  if (!count($author)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book must have at least one author!']);
                        exit;
                  } else {
                        foreach ($author as $x) {
                              if (strlen($x) > 255) {
                                    http_response_code(400);
                                    echo json_encode(['error' => 'Author name must be at most 255 characters long or less!']);
                                    exit;
                              }
                        }
                  }

                  if (!$publisher) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Publisher is empty!']);
                        exit;
                  } else if (strlen($publisher) > 255) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Publisher must be at most 255 characters long or less!']);
                        exit;
                  }

                  if (!$publishDate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Publish date is empty!']);
                        exit;
                  } else {
                        // Create a DateTime object for the date of birth
                        $tempDate = new DateTime($publishDate, new DateTimeZone($_ENV['TIMEZONE']));
                        $tempDate->setTime(0, 0, 0); // Set time to 00:00:00

                        // Get the current date
                        $currentDate = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                        $currentDate->setTime(0, 0, 0); // Set time to 00:00:00

                        if ($tempDate > $currentDate) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Publish date invalid!']);
                              exit;
                        }
                  }

                  if ($description && strlen($description) > 2000) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Description must be at most 2000 characters long or less!']);
                        exit;
                  }

                  if ($physicalPrice && (!is_numeric($physicalPrice) || $physicalPrice <= 0)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Hardcover price invalid!']);
                        exit;
                  }

                  if ($inStock && (!is_numeric($inStock) || $inStock < 0)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Hardcover in stock invalid!']);
                        exit;
                  }

                  if ($filePrice && (!is_numeric($filePrice) || $filePrice <= 0)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'E-book price invalid!']);
                        exit;
                  }

                  $allowedImageTypes = ['image/jpeg', 'image/png'];
                  $allowedFileTypes = ['application/pdf'];

                  if (isset($_FILES['image'])) {
                        if (is_array($_FILES['image']['name'])) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Only submit 1 image file!']);
                              exit;
                        }

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

                  if (isset($_FILES['pdf'])) {
                        if ($removeFile) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Conflict request, please choose either removing the current file or uploading a new one, not both!']);
                              exit;
                        }

                        if (is_array($_FILES['pdf']['name'])) {
                              http_response_code(400);
                              echo json_encode(['error' => 'Only submit 1 PDF file!']);
                              exit;
                        }

                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo === false) {
                              throw new Exception("Failed to open fileinfo database!");
                        }
                        $fileMimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
                        if ($fileMimeType === false) {
                              throw new Exception("Failed to get the MIME type of the PDF file!");
                        }
                        $finfoCloseResult = finfo_close($finfo);
                        if (!$finfoCloseResult) {
                              throw new Exception("Failed to close fileinfo resource!");
                        }

                        if (!in_array($fileMimeType, $allowedFileTypes)) {
                              http_response_code(400);
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

                  $stmt = $conn->prepare('select * from book where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from book where id=?` preparation failed!']);
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
                        http_response_code(500);
                        echo json_encode(['error' => 'Book not found!']);
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select * from book where id!=? and name=? and edition=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from book where id!=? and name=? and edition=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ssi', $id, $name, $edition);
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

                  $stmt = $conn->prepare('select * from book where id!=? and isbn=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select * from book where id!=? and isbn=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ss', $id, $isbn);
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

                  // Begin updating information

                  if (isset($_FILES['image'])) {
                        $imageFile = null;

                        $stmt = $conn->prepare('select imagePath from book where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select imagePath from book where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
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
                                    $path = dirname(dirname(dirname(__DIR__))) . "/data/book/" . $result['imagePath'];
                                    $temp_arr = explode('/', $result['imagePath']);
                                    array_pop($temp_arr);
                                    $imageDir = implode('/', $temp_arr);
                                    $imageFile = implode('/', $temp_arr) . "/{$name}-{$currentDateTime}.{$fileExtension}";
                              } else {
                                    $imageDir = "$id";
                                    $imageFile = "$id/{$name}-{$currentDateTime}.{$fileExtension}";
                              }
                              if (!is_dir(dirname(dirname(dirname(__DIR__))) . "/data/book/" . $imageDir)) {
                                    if (!mkdir(dirname(dirname(dirname(__DIR__))) . "/data/book/" . $imageDir)) {
                                          $conn->rollback();
                                          $conn->close();
                                          throw new Exception("Error occurred during creating directory!");
                                    }
                              }

                              if (!move_uploaded_file($_FILES["image"]["tmp_name"], dirname(dirname(dirname(__DIR__))) . "/data/book/" . $imageFile)) {
                                    $conn->rollback();
                                    $conn->close();
                                    throw new Exception("Error occurred during moving image file!");
                              }

                              $stmt = $conn->prepare('update book set imagePath=? where id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `update book set imagePath=? where id=?` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $imageFile, $id);
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
                                          echo json_encode(['error' => 'Updated more than one book!']);
                                          $stmt->close();
                                          $conn->rollback();
                                          $conn->close();
                                          exit;
                                    }
                                    $stmt->close();
                              }
                        }
                  }

                  $stmt = $conn->prepare('update book set name=?,edition=?,isbn=?,publisher=?,publishDate=?,description=? where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update book set name=?,edition=?,isbn=?,publisher=?,publishDate=?,description=? where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('sssssss', $name, $edition, $isbn, $publisher, $publishDate, $description, $id);
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
                              echo json_encode(['error' => 'Updated more than one book!']);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $stmt = $conn->prepare('delete from author where bookID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `delete from author where bookID=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('insert into author(bookID,authorName) values(?,?)');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `insert into author(bookID,authorName) values(?,?)` preparation failed!']);
                        $conn->close();
                        exit;
                  }
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

                  $stmt = $conn->prepare('delete from belong where bookID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `delete from belong where bookID=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $id);
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->rollback();
                        $conn->close();
                        exit;
                  }
                  $stmt->close();

                  if (count($category)) {
                        $categoryID = [];
                        $stmt = $conn->prepare('select id from category where name=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select id from category where name=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
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
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `insert into belong(bookID,categoryID) values(?,?)` preparation failed!']);
                              $conn->close();
                              exit;
                        }
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

                  $stmt = $conn->prepare('update physicalCopy set price=?,inStock=? where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update physicalCopy set price=?,inStock=? where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('dis', $physicalPrice, $inStock, $id);
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
                              echo json_encode(['error' => 'Updated more than one book!']);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $stmt = $conn->prepare('update fileCopy set price=? where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `update fileCopy set price=? where id=?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ds', $filePrice, $id);
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
                              echo json_encode(['error' => 'Updated more than one book!']);
                              $stmt->close();
                              $conn->rollback();
                              $conn->close();
                              exit;
                        }
                        $stmt->close();
                  }

                  $queryResult = true;

                  if (isset($_FILES['pdf'])) {
                        $pdfFile = null;

                        $stmt = $conn->prepare('select filePath from fileCopy where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select filePath from fileCopy where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
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
                              $fileDir = null;

                              if ($result['filePath']) {
                                    $path = dirname(dirname(dirname(__DIR__))) . "/data/book/" . $result['filePath'];
                                    $temp_arr_1 = explode('/', $result['filePath']);
                                    array_pop($temp_arr_1);
                                    $fileDir = implode('/', $temp_arr_1);
                                    $pdfFile = implode('/', $temp_arr_1) . "/{$name}-{$currentDateTime}.pdf";
                              } else {
                                    $fileDir = "$id";
                                    $pdfFile = "$id/{$name}-{$currentDateTime}.pdf";
                              }
                              if (!is_dir(dirname(dirname(dirname(__DIR__))) . "/data/book/" . $fileDir)) {
                                    if (!mkdir(dirname(dirname(dirname(__DIR__))) . "/data/book/" . $fileDir)) {
                                          $conn->rollback();
                                          $conn->close();
                                          throw new Exception("Error occurred during creating directory!");
                                    }
                              }

                              if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], dirname(dirname(dirname(__DIR__))) . "/data/book/" . $pdfFile)) {
                                    $conn->rollback();
                                    $conn->close();
                                    throw new Exception("Error occurred during moving PDF file!");
                              }

                              $stmt = $conn->prepare('update fileCopy set filePath=? where id=?');
                              if (!$stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `update fileCopy set filePath=? where id=?` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $stmt->bind_param('ss', $pdfFile, $id);
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
                                          echo json_encode(['error' => 'Updated more than one book!']);
                                          $stmt->close();
                                          $conn->rollback();
                                          $conn->close();
                                          exit;
                                    }
                                    $stmt->close();
                                    $queryResult = "https://{$_SERVER['HTTP_HOST']}/data/book/$pdfFile";
                              }
                        }
                  } else if ($removeFile) {
                        $stmt = $conn->prepare('select exists(select * from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where fileOrderContain.bookID=? and customerOrder.status=true) as result');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `select exists(select * from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where fileOrderContain.bookID=? and customerOrder.status=true) as result` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
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

                              if ($result['result'] === 1) {
                                    http_response_code(400);
                                    echo json_encode(['error' => 'This file copy is in an order that has been paid for, you can\'t remove it PDF file, only replacements allowed!']);
                                    $conn->rollback();
                                    $conn->close();
                                    exit;
                              }
                        }

                        $stmt = $conn->prepare('update fileCopy set filePath=null where id=?');
                        if (!$stmt) {
                              http_response_code(500);
                              echo json_encode(['error' => 'Query `update fileCopy set filePath=null where id=?` preparation failed!']);
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('s', $id);
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
                                    echo json_encode(['error' => 'Updated more than one book!']);
                                    $stmt->close();
                                    $conn->rollback();
                                    $conn->close();
                                    exit;
                              }
                              $stmt->close();
                              $queryResult = -1;
                        }
                  }

                  echo json_encode(['query_result' => $queryResult]);

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