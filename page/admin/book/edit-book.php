<?php
require_once __DIR__ . '/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else {
      if (isset($_GET['id'])) {
            require_once __DIR__ . '/../../../config/db_connection.php';
            require_once __DIR__ . '/../../../tool/php/sanitizer.php';
            require_once __DIR__ . '/../../../tool/php/formatter.php';

            try {
                  $id = sanitize($_GET['id']);

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        exit;
                  }

                  $query_result = null;

                  $stmt = $conn->prepare('select book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publisherLink,book.publishDate,book.description,book.imagePath from book where book.id=?');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows < 0 || $result->num_rows > 1) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                  } else if ($result->num_rows === 0) {
                        http_response_code(404);
                        require_once __DIR__ . '/../../../error/404.php';
                  } else {
                        $result = $result->fetch_assoc();
                        $result['isbn'] = formatISBN($result['isbn']);
                        $result['imagePath'] = "https://{$_SERVER['HTTP_HOST']}/data/book/{$result['imagePath']}";
                        $query_result = $result;
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select authorName from author where bookID=? order by authorName,authorIdx');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows < 0) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                  } else if ($result->num_rows === 0) {
                        $query_result['author'] = [];
                  } else {
                        while ($row = $result->fetch_assoc()) {
                              $query_result['author'][] = $row['authorName'];
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows < 0) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                  } else if ($result->num_rows === 0) {
                        $query_result['category'] = [];
                  } else {
                        while ($row = $result->fetch_assoc()) {
                              $temp = [];
                              $temp['name'] = $row['name'];
                              $temp['description'] = $row['description'];
                              $query_result['category'][] = $temp;
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows < 0 || $result->num_rows > 1) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                  } else if ($result->num_rows === 0) {
                        $query_result['physicalCopy'] = [];
                  } else {
                        while ($row = $result->fetch_assoc()) {
                              $query_result['physicalCopy']['price'] = $row['price'];
                              $query_result['physicalCopy']['inStock'] = $row['inStock'];
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                  $stmt->bind_param('s', $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows < 0 || $result->num_rows > 1) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                  } else if ($result->num_rows === 0) {
                        $query_result['fileCopy'] = [];
                  } else {
                        while ($row = $result->fetch_assoc()) {
                              $query_result['fileCopy']['price'] = $row['price'];
                              $query_result['fileCopy']['filePath'] = "https://{$_SERVER['HTTP_HOST']}/data/book/{$row['filePath']}";
                        }
                  }
                  $stmt->close();
$conn->close();
            } catch (Exception $e) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
            }
      } else {
            http_response_code(400);
            require_once __DIR__ . '/../../../error/400.php';
      }
?>

      <!DOCTYPE html>
      <html>

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Nghia Duong">
            <meta name="description" content="Edit book information of NQK Bookstore">
            <title>Edit Book</title>
            <link rel="stylesheet" href="/css/admin/book/book_detail.css">
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='w-100 h-100 d-flex'>
                        <form onsubmit="confirmSubmitForm(event)" class='border border-1 rounded border-dark custom_container m-auto bg-white d-flex flex-column overflow-y-auto overflow-x-hidden'>
                              <div class="ms-auto me-3 mt-xl-3 mb-3 mt-5 order-xl-1 order-2">
                                    <button class="btn btn-secondary ms-1" onclick="resetForm()" type='button'>Reset</button>
                                    <button class="btn btn-success me-1" type='submit'>Save</button>
                              </div>
                              <div class='row flex-grow-1 order-xl-2 order-1'>
                                    <div class="col-xl-5 col-12">
                                          <div class='d-flex flex-column align-items-center w-100 h-100 ps-xl-5 px-3'>
                                                <div class="mt-xl-auto my-2 mt-3">
                                                      <label for="bookNameInput" class="form-label">Book Name:</label>
                                                      <input type="text" class="form-control fs-4" id="bookNameInput" value="<?php echo $query_result['name']; ?>">
                                                </div>
                                                <div class="mb-auto my-2 d-flex flex-column w-100 align-items-center">
                                                      <img class='custom_image w-100' id="bookImage" alt="book image" src="<?php echo $query_result['imagePath']; ?>">
                                                      </img>
                                                      <label class='btn btn-sm btn-light border border-dark mt-3 mx-auto'>
                                                            <input accept='.jpg,.jpeg,.png' id="imageInput" type='file' class='d-none' onchange="setNewImage(event)"></input>
                                                            Browse
                                                      </label>
                                                      <p id="imageFileName" class='mx-auto'></p>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                          <div class='d-flex flex-column ps-xl-5 w-100 h-100'>
                                                <div class="mt-auto mb-2 px-xl-5 px-3">
                                                      <label for="editionInput" class="form-label">Edition:</label>
                                                      <input type="number" min="1" class="form-control" id="editionInput" value="<?php echo $query_result['edition']; ?>">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="isbnInput" class="form-label">ISBN-13:</label>
                                                      <input type="text" class="form-control" id="isbnInput" value="<?php echo $query_result['isbn']; ?>">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="ageInput" class="form-label">Age Restriction:</label>
                                                      <input type="number" min="0" class="form-control" id="ageInput" value="<?php echo $query_result['ageRestriction'] ? $query_result['ageRestriction'] : ''; ?>">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3 d-flex flex-xl-row flex-column row">
                                                      <div class='col'>
                                                            <label for="publisherInput" class="form-label">Publisher:</label>
                                                            <input type="text" class="form-control" id="publisherInput" value="<?php echo $query_result['publisher']; ?>">
                                                      </div>
                                                      <div class="ms-xl-3 mt-2 mt-xl-0 col">
                                                            <label for="publisherLinkInput" class="form-label">Publisher Link:</label>
                                                            <input type="url" class="form-control" id="publisherLinkInput" value="<?php echo $query_result['publisherLink'] ? $query_result['publisherLink'] : ''; ?>">
                                                      </div>
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="publishDateInput" class="form-label">Publish Date:</label>
                                                      <input type="date" class="form-control" id="publishDateInput" value="<?php echo $query_result['publishDate']; ?>">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3 d-flex flex-md-row flex-column row">
                                                      <div class='col'>
                                                            <label for="physicalPriceInput" class="form-label">Physical Copy Price ($):</label>
                                                            <input type="number" min="0" class="form-control" id="physicalPriceInput" value="<?php echo $query_result['physicalCopy']['price']; ?>">
                                                      </div>
                                                      <div class="ms-md-5 mt-2 mt-md-0 col">
                                                            <label for="inStockInput" class="form-label">In Stock:</label>
                                                            <input type="number" min="0" class="form-control" id="inStockInput" value="<?php echo $query_result['physicalCopy']['inStock']; ?>">
                                                      </div>
                                                </div>
                                                <div class="mb-auto mt-2 px-xl-5 px-3 d-flex flex-md-row flex-column row">
                                                      <div class='col'>
                                                            <label for="filePriceInput" class="form-label">File Copy Price ($):</label>
                                                            <input type="number" min="0" class="form-control" id="filePriceInput" value="<?php echo $query_result['fileCopy']['price']; ?>">
                                                      </div>
                                                      <div class="ms-md-5 mt-2 mt-md-0 col">
                                                            <div class="d-flex flex-column">
                                                                  <label class="form-label">
                                                                        PDF (old file
                                                                        <a id='pdfPath' href="<?php echo $query_result['fileCopy']['filePath']; ?>" target="_blank" alt='PDF file'>
                                                                              <i class="bi bi-file-earmark-fill text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Read file"></i>
                                                                        </a>):
                                                                  </label>
                                                                  <div class="d-flex align-items-center">
                                                                        <label class='btn btn-sm btn-light border border-dark'>
                                                                              <input type="file" class="form-control d-none" id="filePathInput" accept='.pdf' onchange="setNewFile(event)">
                                                                              Browse
                                                                        </label>
                                                                        <p class="mb-0 ms-3" id="pdfFileName"></p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </form>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/javascript/admin/book/edit_book.js"></script>
            <script src="/tool/js/sanitizer.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
      </body>

      </html>

<?php } ?>