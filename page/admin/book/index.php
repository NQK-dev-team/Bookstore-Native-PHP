<?php
require_once __DIR__ . '/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/role_check.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
      require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

      $_SESSION['update_book_id'] = null;

      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';
      require_once __DIR__ . '/../../../tool/php/formatter.php';
      require_once __DIR__ . '/../../../tool/php/ratingStars.php';

      try {
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }

            $elem = '';

            $stmt = $conn->prepare('select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath from book where book.status=1 order by book.name,book.id limit 10');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            } else {
                  $result = $stmt->get_result();
                  if ($result->num_rows > 0) {
                        $counter = 0;
                        while ($row = $result->fetch_assoc()) {
                              $counter++;
                              $row['edition'] = convertToOrdinal($row['edition']);
                              $id = $row['id'];
                              $row['isbn'] = formatISBN($row['isbn']);
                              $row['publishDate'] = MDYDateFormat($row['publishDate']);
                              $row['imagePath'] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath'])) . "\"";
                              $row['ageRestriction'] ? $row['ageRestriction'] : 'N/A';
                              $elem .= '<tr>';
                              $elem .= "<td class=\"align-middle\">{$counter}</td>";
                              $elem .= "<td class=\"align-middle\"><img {$row['imagePath']} alt=\"book image\" class=\"book_image\"></img></td>";
                              $elem .= "<td class=\"col-2 align-middle\">{$row['name']}</td>";
                              $elem .= "<td class=\"align-middle\">{$row['edition']}</td>";
                              $elem .= "<td class=\"align-middle\">{$row['isbn']}</td>";
                              $elem .= "<td class=\"align-middle\">{$row['ageRestriction']}</td>";

                              $sub_stmt = $conn->prepare('select authorName from author where bookID=? order by authorName,authorIdx');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              } else {
                                    $sub_result = $sub_stmt->get_result();
                                    if ($sub_result->num_rows > 0) {
                                          $elem .= "<td class=\"col-1 align-middle\">
                                          <div class='d-flex flex-column'>";
                                          while ($sub_row = $sub_result->fetch_assoc()) {
                                                if ($sub_result->num_rows === 1)
                                                      $elem .= "<p class='mb-0'>
                                                      {$sub_row['authorName']}
                                                </p>";
                                                else
                                                      $elem .= "<p>
                                                      {$sub_row['authorName']}
                                                </p>";
                                          }
                                          $elem .= "</div>
                                          </td>";
                                    } else
                                          $elem .= "<td class=\"col-1 align-middle\">N/A</td>";
                              }

                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              } else {
                                    $sub_result = $sub_stmt->get_result();
                                    if ($sub_result->num_rows > 0) {
                                          $elem .= "<td class=\"col-1 align-middle\">
                                          <div class='d-flex flex-column'>";
                                          while ($sub_row = $sub_result->fetch_assoc()) {
                                                $description = $sub_row['description'] ? $sub_row['description'] : 'N/A';
                                                if ($sub_result->num_rows === 1)
                                                      $elem .= "<p class='mb-0'>
                                                      {$sub_row['name']}
                                                      <i class=\"bi bi-question-circle help\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"{$description}\"></i>
                                                </p>";
                                                else
                                                      $elem .= "<p>
                                                      {$sub_row['name']}
                                                      <i class=\"bi bi-question-circle help\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"{$description}\"></i>
                                                </p>";
                                          }
                                          $elem .= "</div>
                                          </td>";
                                    } else
                                          $elem .= "<td class=\"col-1 align-middle\">N/A</td>";
                              }

                              $sub_stmt->close();

                              $elem .= "<td class=\"col-1 align-middle\">
                                    <div class='d-flex flex-column'>
                                          <p>
                                                {$row['publisher']}
                                          </a>
                                          <p>
                                                {$row['publishDate']}   
                                          </p>
                                    </div>
                              </td>";
                              $row['description'] = $row['description'] ? $row['description'] : 'N/A';
                              $elem .= "<td class=\"col-1 align-middle\"><div class='truncate'>{$row['description']}</div></td>";
                              if ($row['avgRating']) {
                                    $func_res = displayRatingStars($row['avgRating']);
                                    $elem .= "<td class=\"align-middle col-1\">
                                          <span><span class='text-warning'>{$func_res}</span>&nbsp;({$row['avgRating']})</span>
                                    </td>";
                              } else
                                    $elem .= "<td class=\"align-middle\">
                                          N/A
                                    </td>";

                              $elem .= "<td class=\"col-1 align-middle\">
                                    <div class='d-flex flex-column'>";
                              $sub_stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if ($isSuccess) {
                                    $sub_result = $sub_stmt->get_result();
                                    if ($sub_result->num_rows === 1) {
                                          $sub_row = $sub_result->fetch_assoc();
                                          $sub_row['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $sub_row['inStock'] = $sub_row['inStock'] ? $sub_row['inStock'] : "N/A";
                                          $elem .= "<p>Physical: {$sub_row['price']} (in stock: {$sub_row['inStock']})</p>";
                                    } else if ($sub_result->num_rows === 0)
                                          $elem .= "<p>Physical: N/A (in stock: N/A)</p>";
                              } else {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if ($isSuccess) {
                                    $sub_result = $sub_stmt->get_result();
                                    if ($sub_result->num_rows === 1) {
                                          $sub_row = $sub_result->fetch_assoc();
                                          $sub_row['filePath'] = $sub_row['filePath'] ? "href=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($sub_row['filePath'])) . "\"" : '';
                                          $target = $sub_row['filePath'] !== '' ? "target='_blank'" : '';
                                          $alt = $sub_row['filePath'] !== '' ? "PDF file" : 'No PDF file';
                                          $tooltip = $sub_row['filePath'] !== '' ? "Read file" : 'No PDF file';
                                          $sub_row['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $elem .= "<p>E-book: {$sub_row['price']} <a $target {$sub_row['filePath']} alt='$alt'>
                                          <i class=\"bi bi-file-earmark-fill text-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"$tooltip\"></i>
                                          </a></p>";
                                    } else if ($sub_result->num_rows === 0)
                                          $elem .= "<p>E-book: N/A <a href='#' alt='No PDF file'>
                                          <i class=\"bi bi-file-earmark-fill text-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"No PDF file\"></i>
                                          </a></p>";
                              } else {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->close();
                              $elem .= "</div></td>";

                              $sub_stmt = $conn->prepare('select (exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('ss', $id, $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    require_once __DIR__ . '/../../../error/500.php';
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              } else {
                                    $sub_result = $sub_stmt->get_result();
                                    $sub_result = $sub_result->fetch_assoc();
                                    if ($sub_result['result'])
                                          $elem .= "<td class='align-middle col-1'>
                                                      <div class='d-flex flex-lg-row flex-column'>
                                                            <a class='btn btn-info btn-sm' href='./edit-book?id=$id' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\">
                                                                  <i class=\"bi bi-info-circle text-white\"></i>
                                                            </a>
                                                            <button onclick='confirmDeactivateBook(\"$id\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Deactive\">
                                                                  <i class=\"bi bi-power text-white\"></i>
                                                            </button>
                                                      </div>
                                                </td>";
                                    else
                                          $elem .= "<td class='align-middle col-1'>
                                                      <div class='d-flex flex-lg-row flex-column'>
                                                            <a class='btn btn-info btn-sm' href='./edit-book?id=$id' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\">
                                                                  <i class=\"bi bi-info-circle text-white\"></i>
                                                            </a>
                                                            <button onclick='confirmDeactivateBook(\"$id\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Deactive\">
                                                                  <i class=\"bi bi-power text-white\"></i>
                                                            </button>
                                                            <button onclick='confirmDeleteBook(\"$id\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\">
                                                                  <i class=\"bi bi-trash text-white\"></i>
                                                            </button>
                                                      </div>
                                                </td>";
                              }
                              $elem .= '</tr>';
                        }
                  }
            }
            $stmt->close();

            $stmt = $conn->prepare('select count(*) as totalBook from book where status=1');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            } else {
                  $result = $stmt->get_result();
                  $result = $result->fetch_assoc();
                  $totalEntries = $result['totalBook'];
            }
            $stmt->close();

            $stmt = $conn->prepare('select name from category order by name,id');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $categoryList = '';
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                  $categoryList .= "<li class='categoryHover pointer' onclick='chooseCategory(event)'>{$row['name']}</li>";
            }
            $stmt->close();
            $conn->close();
      } catch (Exception $e) {
            http_response_code(500);
            require_once __DIR__ . '/../../../error/500.php';
            exit;
      }
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Nghia Duong">
            <meta name="description" content="Manage books of NQK Bookstore">
            <title>Manage Books</title>
            <link rel="stylesheet" href="/css/admin/book/book_list.css">
            <?php storeToken(); ?>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class="container-fluid h-100 d-flex flex-column">
                        <h1 class='fs-2 mx-auto mt-3'>Book List</h1>
                        <div class='mt-2 d-flex flex-column flex-lg-row align-items-center'>
                              <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1" role="search" id="search_form">
                                    <button class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                          <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                </g>
                                          </svg>
                                    </button>

                                    <input id="search_book" class="form-control me-2" type="search" placeholder="Search by name, author or ISBN number" aria-label="Search">
                              </form>
                              <div class="mx-auto mx-lg-0 ms-lg-2 order-1 order-lg-2">
                                    <a class="btn btn-success btn-sm" href="./add-book"><strong>+</strong> Add New Book</a>
                              </div>
                        </div>
                        <div class="mt-2">
                              <div class="d-flex align-items-center">
                                    <p class="mb-0 me-2">Category</p>
                                    <div>
                                          <div class="dropdown" id='categoryDropDown'>
                                                <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                      Select category
                                                </button>
                                                <ul class="dropdown-menu dropdownCategory">
                                                      <li class="container">
                                                            <form id='searchCategoryForm'>
                                                                  <input class="form-control" id="categoryInput" type="text" placeholder="Search...">
                                                            </form>
                                                      </li>
                                                      <li>
                                                            <ul class='categories w-100 container mt-2'>
                                                                  <?php
                                                                  echo $categoryList;
                                                                  ?>
                                                            </ul>
                                                      </li>
                                                </ul>
                                          </div>
                                    </div>
                              </div>
                        </div>
                        <div class="mt-2">
                              <div class="d-flex align-items-center">
                                    <p class="mb-0 me-2">Show</p>
                                    <div>
                                          <select id="entry_select" class="form-select pointer" aria-label="Entry selection" onchange="selectEntry()">
                                                <option value=10 selected>10</option>
                                                <option value=25>25</option>
                                                <option value=50>50</option>
                                                <option value=100>100</option>
                                          </select>
                                    </div>
                                    <p class="mb-0 ms-2">entries</p>
                              </div>
                              <div class="mt-2">
                                    <div class="form-check form-switch">
                                          <label class="form-check-label text-success" for="flexSwitchCheckDefault" id="switch_label">Choose active books</label>
                                          <input title='Book status' class="form-check-input pointer" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked onchange="updateSwitchLabel()">
                                    </div>
                              </div>
                        </div>
                        <div class="w-100 overflow-x-auto">
                              <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                    <thead>
                                          <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Image</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Edition</th>
                                                <th scope="col">ISBN-13</th>
                                                <th scope="col">Age Restriction</th>
                                                <th scope="col">Author</th>
                                                <th scope="col">Category</th>
                                                <th scope="col">Publisher</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Rating</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Action</th>
                                          </tr>
                                    </thead>
                                    <tbody id="table_body">
                                          <?php
                                          echo $elem;
                                          ?>
                                    </tbody>
                              </table>
                        </div>
                        <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                              <div class="d-flex">
                                    <p>Show&nbsp;</p>
                                    <p id="start_entry">
                                          <?php
                                          if ($totalEntries === 0) echo '0';
                                          else echo '1'; ?>
                                    </p>
                                    <p>&nbsp;to&nbsp;</p>
                                    <p id="end_entry">
                                          <?php
                                          if ($totalEntries < 10) echo $totalEntries;
                                          else echo '10'; ?>
                                    </p>
                                    <p>&nbsp;of&nbsp;</p>
                                    <p id="total_entries"><?php echo $totalEntries; ?></p>
                                    <p>&nbsp;entries</p>
                              </div>
                              <div class="group_button">
                                    <div class="btn-group d-flex" role="group">
                                          <button type="button" class="btn btn-outline-info" id="prev_button" onClick="changeList(false)" disabled>Previous</button>
                                          <button type="button" class="btn btn-info text-white" disabled id="list_offset">1</button>
                                          <button type="button" class="btn btn-outline-info" id="next_button" onClick="changeList(true)" <?php if ($totalEntries !== "N/A" && 10 >= $totalEntries) echo 'disabled'; ?>>Next</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Error!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p id="error_message"></p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Deactivation</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to deactivate this book?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" onclick="deactivateBook()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Activation</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to activate this book?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" onclick="activateBook()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Deletion</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to delete this book?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" onclick="deleteBook()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/javascript/admin/book/book_list.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
      </body>

      </html>

<?php } ?>