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
      unset($_SESSION['update_book_id']);
      unset($_SESSION['update_customer_id']);
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
            <meta name="description" content="Get statistics about books of NQK Bookstore">
            <title>Statistics</title>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/2.0.1/chartjs-plugin-zoom.min.js"></script>
            <link rel="stylesheet" href="/css/admin/statistic/style.css">
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='w-100 d-flex flex-column h-100'>
                        <h1 class='mx-auto mt-2'>Book Statistics</h1>
                        <div class='mt-3 d-flex flex-md-row flex-column align-items-md-center container-fluid mb-3'>
                              <div class='d-flex align-items-center'>
                                    <label for='startDateInput' class="me-2 form-label mb-0 fw-bold fs-5">Start</label>
                                    <input type="date" class="form-control w-auto pointer" id="startDateInput" aria-describedby="Start Date Input">
                              </div>
                              <div class='d-flex align-items-center mt-2 mt-md-0 mx-md-2'>
                                    <label for='endDateInput' class="me-2 form-label mb-0 fw-bold fs-5">End&nbsp;</label>
                                    <input type="date" class="form-control w-auto pointer" id="endDateInput" aria-describedby="End Date Input">
                              </div>
                              <div>
                                    <button class='btn btn-sm mt-2 mt-md-0 btn-success' onclick="selectBookEntry(); getCategoryChart();">Get Statistics</button>
                              </div>
                        </div>
                        <div class='container-fluid'>
                              <h3 class='mt-4'>Sale By Category</h3>
                              <div class='w-100 overflow-auto mt-3'>
                                    <canvas id='category_chart'></canvas>
                              </div>
                              <h3 class='mt-4'>Detail Book Sale</h3>
                              <div class='mt-2 d-flex align-items-center'>
                                    <form class="d-flex align-items-center w-100 search_form mt-2" role="search" id="search_book_form">
                                          <button title='submit search form' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                      <g id="SVGRepo_iconCarrier">
                                                            <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                      </g>
                                                </svg>
                                          </button>

                                          <input id="search_book" class="form-control me-2" type="search" placeholder="Search by book name, author or ISBN number" aria-label="Search">
                                    </form>
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
                                                                  <ul class='categories w-100 container mt-2' id='category_list'>
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
                                                <select id="book_entry_select" class="form-select pointer" aria-label="Entry selection" onchange="selectBookEntry()">
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
                              <div class='w-100 overflow-x-auto'>
                                    <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                          <thead>
                                                <tr>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Image</th>
                                                      <th scope="col">Name</th>
                                                      <th scope="col">Edition</th>
                                                      <th scope="col">ISBN-13</th>
                                                      <th scope="col" class='text-nowrap'>Age Restriction</th>
                                                      <th scope="col">Author</th>
                                                      <th scope="col">Category</th>
                                                      <th scope="col">Publisher</th>
                                                      <th scope="col">Description</th>
                                                      <th scope="col">Rating</th>
                                                      <th scope="col">Price</th>
                                                      <th scope="col">Sold</th>
                                                </tr>
                                          </thead>
                                          <tbody id="book_table_body">
                                          </tbody>
                                    </table>
                              </div>
                              <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                                    <div class="d-flex">
                                          <p>Show&nbsp;</p>
                                          <p id="book_start_entry">1</p>
                                          <p>&nbsp;to&nbsp;</p>
                                          <p id="book_end_entry">10</p>
                                          <p>&nbsp;of&nbsp;</p>
                                          <p id="book_total_entries"></p>
                                          <p>&nbsp;entries</p>
                                    </div>
                                    <div class="group_button">
                                          <div class="btn-group d-flex" role="group">
                                                <button type="button" class="btn btn-outline-info" id="book_prev_button" onClick="changeBookList(false)" disabled>Previous</button>
                                                <button type="button" class="btn btn-info text-white" disabled id="book_list_offset">1</button>
                                                <button type="button" class="btn btn-outline-info" id="book_next_button" onClick="changeBookList(true)">Next</button>
                                          </div>
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
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script>
                  $(document).ready(function() {
                        const currentDate = new Date().toLocaleString("en-US", {
                              timeZone: "Asia/Ho_Chi_Minh",
                              year: 'numeric',
                              month: '2-digit',
                              day: '2-digit'
                        });
                        const dateParts = currentDate.split("/");
                        const formattedDate = `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;
                        $('#startDateInput').val(formattedDate);
                        $('#endDateInput').val(formattedDate);
                  });
            </script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/generate_color.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
            <script src="/javascript/admin/statistic/book_list.js"></script>
            <script src="/javascript/admin/statistic/category_chart.js"></script>
      </body>

      </html>

<?php } ?>