<?php
require_once __DIR__ . '/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../../error/403.php';
} else {
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';

      try {

            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require __DIR__ . '/../../../error/500.php';
                  exit;
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
                  <meta name="description" content="Manage books of NQK Bookstore">
                  <title>Manage Books</title>
                  <link rel="stylesheet" href="/css/admin/book/styles.css">
            </head>

            <body>
                  <?php
                  require_once __DIR__ . '/../../../layout/admin/header.php';
                  ?>
                  <section id="page">
                        <div class="container-fluid h-100 d-flex flex-column">
                              <form class="d-flex align-items-center mt-2 w-100 search_form mx-auto" role="search" id="search_form">
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
                              <div class="mx-auto mt-3">
                                    <a class="btn btn-success" href="./add-book"><strong>+</strong> Add New Book</a>
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
                                                <input class="form-check-input pointer" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked onchange="updateSwitchLabel()">
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
                                                $stmt = $conn->prepare('select book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publisherLink,book.publishDate,book.description,book.imagePath from book where book.status=1 order by book.name,book.id limit 10');
                                                $stmt->execute();

                                                $result = $stmt->get_result();
                                                if ($result->num_rows > 0) {
                                                      $counter = 0;
                                                      while ($row = $result->fetch_assoc()) {
                                                            $counter++;
                                                            $ordinal = convertToOrdinal($row['edition']);
                                                            $id = $row['id'];
                                                            $formatISBN = formatISBN($row['isbn']);
                                                            $formatDate = convertDateFormat($row['publishDate']);
                                                            echo '<tr>';
                                                            echo "<td class=\"align-middle\">{$counter}</td>";
                                                            echo "<td class=\"align-middle\"><img src=\"https://{$_SERVER['HTTP_HOST']}/data/book/{$row['imagePath']}\" alt=\"book image\" class=\"book_image\"></img></td>";
                                                            echo "<td class=\"col-2 align-middle\">{$row['name']}</td>";
                                                            echo "<td class=\"align-middle\">{$ordinal}</td>";
                                                            echo "<td class=\"align-middle\">{$formatISBN}</td>";
                                                            echo "<td class=\"align-middle\">{$row['ageRestriction']}</td>";

                                                            $sub_stmt = $conn->prepare('select authorName from author where bookID=? order by authorName,authorIdx');
                                                            $sub_stmt->bind_param('s', $id);
                                                            $sub_stmt->execute();
                                                            $sub_result = $sub_stmt->get_result();
                                                            if ($sub_result->num_rows > 0) {
                                                                  echo "<td class=\"col-1 align-middle\">
                                                            <div class='d-flex flex-column'>";
                                                                  while ($sub_row = $sub_result->fetch_assoc()) {
                                                                        if ($sub_result->num_rows === 1)
                                                                              echo "<p class='mb-0'>
                                                                  {$sub_row['authorName']}
                                                                  </p>";
                                                                        else
                                                                              echo "<p>
                                                                  {$sub_row['authorName']}
                                                                  </p>";
                                                                  }
                                                                  echo "</div>
                                                            </td>";
                                                            } else
                                                                  echo "<td class=\"col-1 align-middle\">N/A</td>";

                                                            $sub_stmt->close();

                                                            $sub_stmt = $conn->prepare('select category.name from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                                                            $sub_stmt->bind_param('s', $id);
                                                            $sub_stmt->execute();
                                                            $sub_result = $sub_stmt->get_result();
                                                            if ($sub_result->num_rows > 0) {
                                                                  echo "<td class=\"col-1 align-middle\">
                                                            <div class='d-flex flex-column'>";
                                                                  while ($sub_row = $sub_result->fetch_assoc()) {
                                                                        if ($sub_result->num_rows === 1)
                                                                              echo "<p class='mb-0'>
                                                                  {$sub_row['name']}
                                                                  </p>";
                                                                        else
                                                                              echo "<p>
                                                                  {$sub_row['name']}
                                                                  </p>";
                                                                  }
                                                                  echo "</div>
                                                            </td>";
                                                            } else
                                                                  echo "<td class=\"col-1 align-middle\">N/A</td>";

                                                            $sub_stmt->close();

                                                            echo "<td class=\"col-1 align-middle\">
                                                      <div class='d-flex flex-column'>
                                                            <p>
                                                            {$row['publisher']}
                                                            </p>
                                                            <p>
                                                            {$formatDate}   
                                                            </p>
                                                      </div>
                                                      </td>";
                                                            $bookDescription = $row['description'] ? $row['description'] : 'N/A';
                                                            echo "<td class=\"col-1 align-middle\">{$bookDescription}</td>";
                                                            echo "<td class=\"align-middle\">
                                                      <svg fill='#ffee00' width='24px' height='24px' viewBox='0 0 1024 1024' xmlns='http://www.w3.org/2000/svg' class='icon' stroke='#ffee00'><g id='SVGRepo_bgCarrier' stroke-width='0'></g><g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g><g id='SVGRepo_iconCarrier'> <path d='M908.1 353.1l-253.9-36.9L540.7 86.1c-3.1-6.3-8.2-11.4-14.5-14.5-15.8-7.8-35-1.3-42.9 14.5L369.8 316.2l-253.9 36.9c-7 1-13.4 4.3-18.3 9.3a32.05 32.05 0 0 0 .6 45.3l183.7 179.1-43.4 252.9a31.95 31.95 0 0 0 46.4 33.7L512 754l227.1 119.4c6.2 3.3 13.4 4.4 20.3 3.2 17.4-3 29.1-19.5 26.1-36.9l-43.4-252.9 183.7-179.1c5-4.9 8.3-11.3 9.3-18.3 2.7-17.5-9.5-33.7-27-36.3z'></path> </g></svg>
                                                      {$row['avgRating']}
                                                      </td>";

                                                            echo "<td class=\"col-1 align-middle\">
                                                      <div class='d-flex flex-column'>";
                                                            $sub_stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                                                            $sub_stmt->bind_param('s', $id);
                                                            $sub_stmt->execute();
                                                            $sub_result = $sub_stmt->get_result();
                                                            if ($sub_result->num_rows === 1) {
                                                                  $sub_row = $sub_result->fetch_assoc();
                                                                  echo "<p>Physical: \${$sub_row['price']} (in stock: {$sub_row['inStock']})</p>";
                                                            } else if ($sub_result->num_rows === 0)
                                                                  echo "<p>Physical: N/A (in stock: N/A)</p>";
                                                            else
                                                                  echo "<p>Physical: Error!</p>";
                                                            $sub_stmt->close();

                                                            $sub_stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                                                            $sub_stmt->bind_param('s', $id);
                                                            $sub_stmt->execute();
                                                            $sub_result = $sub_stmt->get_result();
                                                            if ($sub_result->num_rows === 1) {
                                                                  $sub_row = $sub_result->fetch_assoc();
                                                                  echo "<p>PDF: \${$sub_row['price']} <a target='_blank' href='https://{$_SERVER['HTTP_HOST']}/data/book/{$sub_row['filePath']}' alt='PDF file'>
                                                            <svg width='16px' height='16px' viewBox='-3 0 32 32' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:sketch='http://www.bohemiancoding.com/sketch/ns' fill='#000000'><g id='SVGRepo_bgCarrier' stroke-width='0'></g><g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g><g id='SVGRepo_iconCarrier'> <title>file-document</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id='Page-1' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd' sketch:type='MSPage'> <g id='Icon-Set-Filled' sketch:type='MSLayerGroup' transform='translate(-156.000000, -101.000000)' fill='#7d7d7d'> <path d='M176,109 C174.896,109 174,108.104 174,107 L174,103 L180,109 L176,109 L176,109 Z M174,101 L174,101.028 C173.872,101.028 160,101 160,101 C157.791,101 156,102.791 156,105 L156,129 C156,131.209 157.791,133 160,133 L178,133 C180.209,133 182,131.209 182,129 L182,111 L182,109 L174,101 L174,101 Z' id='file-document' sketch:type='MSShapeGroup'> </path> </g> </g> </g></svg>
                                                            </a></p>";
                                                            } else if ($sub_result->num_rows === 0)
                                                                  echo "<p>PDF: N/A <a href='#' alt='No PDF file'>
                                                            <svg width='16px' height='16px' viewBox='-3 0 32 32' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:sketch='http://www.bohemiancoding.com/sketch/ns' fill='#000000'><g id='SVGRepo_bgCarrier' stroke-width='0'></g><g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g><g id='SVGRepo_iconCarrier'> <title>file-document</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id='Page-1' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd' sketch:type='MSPage'> <g id='Icon-Set-Filled' sketch:type='MSLayerGroup' transform='translate(-156.000000, -101.000000)' fill='#7d7d7d'> <path d='M176,109 C174.896,109 174,108.104 174,107 L174,103 L180,109 L176,109 L176,109 Z M174,101 L174,101.028 C173.872,101.028 160,101 160,101 C157.791,101 156,102.791 156,105 L156,129 C156,131.209 157.791,133 160,133 L178,133 C180.209,133 182,131.209 182,129 L182,111 L182,109 L174,101 L174,101 Z' id='file-document' sketch:type='MSShapeGroup'> </path> </g> </g> </g></svg>
                                                            </a></p>";
                                                            else
                                                                  echo "<p>PDF: Error!</p>";
                                                            $sub_stmt->close();
                                                            echo "</div>
                                                      </td>";

                                                            echo "<td class='align-middle'>
                                                      <div class='d-flex flex-lg-row flex-column'>
                                                            <a class='btn btn-info' href='./edit-book?id=$id'>
                                                                  <svg width='20px' height='20px' viewBox='0 0 24.00 24.00' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                        <g id='SVGRepo_bgCarrier' stroke-width='0'></g>
                                                                        <g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g>
                                                                        <g id='SVGRepo_iconCarrier'>
                                                                              <path d='M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z' stroke='#ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'></path>
                                                                              <path d='M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13' stroke='#ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'></path>
                                                                        </g>
                                                                  </svg>
                                                            </a>
                                                            <button onclick='confirmDeleteBook(\"$id\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0'>
                                                                  <svg width='20px' height='20px' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                        <g id='SVGRepo_bgCarrier' stroke-width='0'></g>
                                                                        <g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g>
                                                                        <g id='SVGRepo_iconCarrier'>
                                                                              <path d='M10 12L14 16M14 12L10 16M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6' stroke='#ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'></path>
                                                                        </g>
                                                                  </svg>
                                                            </button>
                                                      </div>
                                                </td>";
                                                            echo '</tr>';
                                                      }
                                                }
                                                $stmt->close();

                                                $stmt = $conn->prepare('select count(*) as totalBook from book where status=1');
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                if ($result->num_rows > 0) {
                                                      $result = $result->fetch_assoc();
                                                      $totalEntries = $result['totalBook'];
                                                } else
                                                      $totalEntries = "N/A";
                                                $conn->close();
                                                ?>
                                          </tbody>
                                    </table>
                              </div>
                              <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                                    <div class="d-flex">
                                          <p>Show&nbsp;</p>
                                          <p id="start_entries">1</p>
                                          <p>&nbsp;to&nbsp;</p>
                                          <p id="entries_number">10</p>
                                          <p>&nbsp;of <?php echo $totalEntries; ?> entries</p>
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
                        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                          <div class="modal-header">
                                                <h1 class="modal-title fs-5">Error!</h1>
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
                  </section>
                  <?php
                  require_once __DIR__ . '/../../../layout/footer.php';
                  ?>
                  <script src="/javascript/admin/menu_after_load.js"></script>
                  <script src="/javascript/admin/book/book_list.js"></script>
                  <script src="/tool/js/input_parser.js"></script>
                  <script src="/tool/js/sanitizer.js"></script>
            </body>

            </html>

<?php } catch (Exception $e) {
            http_response_code(500);
            require __DIR__ . '/../../../error/500.php';
      }
} ?>