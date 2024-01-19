
<?php
require_once __DIR__ . '/../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../config/db_connection.php';

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
            $_POST['removeFile']
      )) {
            try {
                  // Resume the session
                  session_start();

                  $id = $_SESSION['update_book_id'];
                  $name = sanitize(rawurldecode($_POST['name']));
                  $edition = sanitize(rawurldecode($_POST['edition']));
                  $isbn = sanitize(str_replace('-', '', rawurldecode($_POST['isbn'])));
                  $age = sanitize(rawurldecode($_POST['age'])) ? sanitize(rawurldecode($_POST['age'])) : null;
                  $author =  array_map('map', explode(',', $_POST['author']));
                  $category = array_map('map', explode(',', $_POST['category']));
                  $publisher = sanitize(rawurldecode($_POST['publisher']));
                  $publishDate = sanitize(rawurldecode($_POST['publishDate']));
                  $description = sanitize(rawurldecode($_POST['description'])) ? sanitize(rawurldecode($_POST['description'])) : null;
                  $physicalPrice = sanitize(rawurldecode($_POST['physicalPrice'])) ? sanitize(rawurldecode($_POST['physicalPrice'])) : null;
                  $filePrice = sanitize(rawurldecode($_POST['filePrice'])) ? sanitize(rawurldecode($_POST['filePrice'])) : null;
                  $inStock = sanitize(rawurldecode($_POST['inStock'])) ? sanitize(rawurldecode($_POST['inStock'])) : null;
                  $removeFile = filter_var(sanitize(rawurlencode($_POST['removeFile'])), FILTER_VALIDATE_BOOLEAN);

                  if (!$id) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Server can\'t find this book ID!']);
                        exit;
                  }

                  if (!$name) {
                        echo json_encode(['error' => 'Book name is empty!']);
                        exit;
                  } else if (preg_match('/[?\/]/', $name)) {
                        echo json_encode(['error' => 'Book name must not contain \'?\', \'/\' or \'\\\' characters!']);
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
                  } else if (!preg_match('/^[0-9]{13}$/', $isbn)) {
                        echo json_encode(['error' => 'Book ISBN-13 invalid!']);
                        exit;
                  }

                  if ($age && (!is_numeric($age) || $age <= 0)) {
                        echo json_encode(['error' => 'Age restriction invalid!']);
                        exit;
                  }

                  if (!count($author)) {
                        echo json_encode(['error' => 'Book must have at least one author!']);
                        exit;
                  }

                  if (!$publisher) {
                        echo json_encode(['error' => 'Publisher is empty!']);
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
                        $fileMimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                        finfo_close($finfo);

                        if (!in_array($fileMimeType, $allowedImageTypes)) {
                              echo json_encode(['error' => 'Invalid image file!']);
                              exit;
                        } else if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                              echo json_encode(['error' => 'Image size too large!']);
                              exit;
                        }
                  }

                  if (isset($_FILES['pdf'])) {
                        if ($removeFile) {
                              echo json_encode(['error' => 'Conflict request, please choose removing the old file or uploading a new one, not both!']);
                              exit;
                        }

                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $fileMimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
                        finfo_close($finfo);

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

                  $stmt = $conn->prepare('select * from book where id=?');
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

                              $currentDateTime = date("YmdHis");
                              $fileExtension = $_FILES['image']['type'] === 'image/png' ? 'png' : 'jpeg';
                              $imageDir = null;

                              if ($result['imagePath']) {
                                    $path = dirname(dirname(__DIR__)) . "/data/book/" . $result['imagePath'];
                                    $temp_arr = explode('/', $result['imagePath']);
                                    array_pop($temp_arr);
                                    $imageDir = implode('/', $temp_arr);
                                    $imageFile = implode('/', $temp_arr) . "/{$name}-{$currentDateTime}.{$fileExtension}";
                              } else {
                                    $imageDir = "$id";
                                    $imageFile = "$id/{$name}-{$currentDateTime}.{$fileExtension}";
                              }
                              if (!is_dir(dirname(dirname(__DIR__)) . "/data/book/" . $imageDir)) {
                                    mkdir(dirname(dirname(__DIR__)) . "/data/book/" . $imageDir);
                              }

                              move_uploaded_file($_FILES["image"]["tmp_name"], dirname(dirname(__DIR__)) . "/data/book/" . $imageFile);

                              $stmt->close();

                              $stmt = $conn->prepare('update book set imagePath=? where id=?');
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

                  $stmt = $conn->prepare('update book set name=?,edition=?,isbn=?,publisher=?,publishDate=?,ageRestriction=?,description=? where id=?');
                  $stmt->bind_param('ssssssss', $name, $edition, $isbn, $publisher, $publishDate, $age, $description, $id);
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
                  foreach ($author as $x) {
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

                  $categoryID = [];
                  $stmt = $conn->prepare('select id from category where name=?');
                  foreach ($category as $x) {
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
                  $stmt->close();

                  $stmt = $conn->prepare('delete from belong where bookID=?');
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

                  $stmt = $conn->prepare('update physicalCopy set price=?,inStock=? where id=?');
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

                              $currentDateTime = date("YmdHis");
                              $fileDir = null;

                              if ($result['filePath']) {
                                    $path = dirname(dirname(__DIR__)) . "/data/book/" . $result['filePath'];
                                    $temp_arr_1 = explode('/', $result['filePath']);
                                    array_pop($temp_arr_1);
                                    $fileDir = implode('/', $temp_arr_1);
                                    $pdfFile = implode('/', $temp_arr_1) . "/{$name}-{$currentDateTime}.pdf";
                              } else {
                                    $fileDir = "$id";
                                    $pdfFile = "$id/{$name}-{$currentDateTime}.pdf";
                              }
                              if (!is_dir(dirname(dirname(__DIR__)) . "/data/book/" . $fileDir)) {
                                    mkdir(dirname(dirname(__DIR__)) . "/data/book/" . $fileDir);
                              }

                              move_uploaded_file($_FILES["pdf"]["tmp_name"], dirname(dirname(__DIR__)) . "/data/book/" . $pdfFile);

                              $stmt->close();

                              $stmt = $conn->prepare('update fileCopy set filePath=? where id=?');
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
                  }

                  if ($removeFile) {
                        $stmt = $conn->prepare('update fileCopy set filePath=null where id=?');
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