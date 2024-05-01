<?php
require_once __DIR__ . '/../../tool/php/login_check.php';
require_once __DIR__ . '/../../tool/php/role_check.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../error/403.php';
} else if ($return_status_code === 200) {
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../head_element/cdn.php';
            require_once __DIR__ . '/../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Nghia Duong">
            <meta name="description" content="Home page of NQK bookstore for admins">
            <title>Overall Statistics</title>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/2.0.1/chartjs-plugin-zoom.min.js"></script>
            <link rel="stylesheet" href="/css/admin/home/style.css">
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='w-100 h-100 d-flex flex-column'>
                        <div class='w-100 mt-5 d-flex flex-column'>
                              <h4 class='mx-auto text-center'>This Week Best Selling Books</h4>
                              <div class='bookCarouselSlides'>
                                    <div id="bookCarousel" class="carousel slide carousel-dark">
                                          <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                      <div class="card w-100 d-flex flex-lg-row">
                                                            <img id='book_image_1' src='/image/no_image.png' class="mx-lg-5 mx-auto bookImg my-2" alt="Book 1 image">
                                                            <div class="card-body mb-5 px-5">
                                                                  <h5 class="card-title" id='book_name_1'>N/A</h5>
                                                                  <div class="card-text d-flex">
                                                                        <p>Edition:&nbsp;</p>
                                                                        <p id='book_edition_1'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>ISBN-13:&nbsp;</p>
                                                                        <p id='book_isbn_1'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Author:&nbsp;</p>
                                                                        <p id='book_author_1'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Category:&nbsp;</p>
                                                                        <p id='book_category_1'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Publisher:&nbsp;</p>
                                                                        <p id='book_publisher_1'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Sold:&nbsp;</p>
                                                                        <p id='book_sold_1' class='fw-medium'>N/A</p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <div class="carousel-item">
                                                      <div class="card w-100 d-flex flex-lg-row">
                                                            <img id='book_image_2' src='/image/no_image.png' class="mx-lg-5 mx-auto bookImg my-2" alt="Book 2 image">
                                                            <div class="card-body mb-5 px-5">
                                                                  <h5 class="card-title" id='book_name_2'>N/A</h5>
                                                                  <div class="card-text d-flex">
                                                                        <p>Edition:&nbsp;</p>
                                                                        <p id='book_edition_2'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>ISBN-13:&nbsp;</p>
                                                                        <p id='book_isbn_2'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Author:&nbsp;</p>
                                                                        <p id='book_author_2'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Category:&nbsp;</p>
                                                                        <p id='book_category_2'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Publisher:&nbsp;</p>
                                                                        <p id='book_publisher_2'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Sold:&nbsp;</p>
                                                                        <p id='book_sold_2' class='fw-medium'>N/A</p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <div class="carousel-item">
                                                      <div class="card w-100 d-flex flex-lg-row">
                                                            <img id='book_image_3' src='/image/no_image.png' class="mx-lg-5 mx-auto bookImg my-2" alt="Book 3 image">
                                                            <div class="card-body mb-5 px-5">
                                                                  <h5 class="card-title" id='book_name_3'>N/A</h5>
                                                                  <div class="card-text d-flex">
                                                                        <p>Edition:&nbsp;</p>
                                                                        <p id='book_edition_3'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>ISBN-13:&nbsp;</p>
                                                                        <p id='book_isbn_3'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Author:&nbsp;</p>
                                                                        <p id='book_author_3'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Category:&nbsp;</p>
                                                                        <p id='book_category_3'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Publisher:&nbsp;</p>
                                                                        <p id='book_publisher_3'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Sold:&nbsp;</p>
                                                                        <p id='book_sold_3' class='fw-medium'>N/A</p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <div class="carousel-item">
                                                      <div class="card w-100 d-flex flex-lg-row">
                                                            <img id='book_image_4' src='/image/no_image.png' class="mx-lg-5 mx-auto bookImg my-2" alt="Book 4 image">
                                                            <div class="card-body mb-5 px-5">
                                                                  <h5 class="card-title" id='book_name_4'>N/A</h5>
                                                                  <div class="card-text d-flex">
                                                                        <p>Edition:&nbsp;</p>
                                                                        <p id='book_edition_4'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>ISBN-13:&nbsp;</p>
                                                                        <p id='book_isbn_4'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Author:&nbsp;</p>
                                                                        <p id='book_author_4'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Category:&nbsp;</p>
                                                                        <p id='book_category_4'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Publisher:&nbsp;</p>
                                                                        <p id='book_publisher_4'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Sold:&nbsp;</p>
                                                                        <p id='book_sold_4' class='fw-medium'>N/A</p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <div class="carousel-item">
                                                      <div class="card w-100 d-flex flex-lg-row">
                                                            <img id='book_image_5' src='/image/no_image.png' class="mx-lg-5 mx-auto bookImg my-2" alt="Book 5 image">
                                                            <div class="card-body mb-5 px-5">
                                                                  <h5 class="card-title" id='book_name_5'>N/A</h5>
                                                                  <div class="card-text d-flex">
                                                                        <p>Edition:&nbsp;</p>
                                                                        <p id='book_edition_5'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>ISBN-13:&nbsp;</p>
                                                                        <p id='book_isbn_5'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Author:&nbsp;</p>
                                                                        <p id='book_author_5'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Category:&nbsp;</p>
                                                                        <p id='book_category_5'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Publisher:&nbsp;</p>
                                                                        <p id='book_publisher_5'>N/A</p>
                                                                  </div>
                                                                  <div class="card-text d-flex">
                                                                        <p>Sold:&nbsp;</p>
                                                                        <p id='book_sold_5' class='fw-medium'>N/A</p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <div class="carousel-indicators position-">
                                                      <button type="button" data-bs-target="#bookCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Book 1"></button>
                                                      <button type="button" data-bs-target="#bookCarousel" data-bs-slide-to="1" aria-label="Book 2"></button>
                                                      <button type="button" data-bs-target="#bookCarousel" data-bs-slide-to="2" aria-label="Book 3"></button>
                                                      <button type="button" data-bs-target="#bookCarousel" data-bs-slide-to="3" aria-label="Book 4"></button>
                                                      <button type="button" data-bs-target="#bookCarousel" data-bs-slide-to="4" aria-label="Book 5"></button>

                                                </div>
                                          </div>
                                          <button class="carousel-control-prev w-auto ms-1" type="button" data-bs-target="#bookCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                          </button>
                                          <button class="carousel-control-next w-auto me-1" type="button" data-bs-target="#bookCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                          </button>
                                    </div>
                              </div>
                        </div>
                        <div class='w-100 mt-5 d-flex flex-column'>
                              <h4 class='mx-auto text-center mt-5'>This Week Best Selling Categories</h4>
                              <div class='container-fluid overflow-auto'>
                                    <canvas id='category_chart'></canvas>
                              </div>
                        </div>
                        <div class='w-100 mt-5 d-flex flex-column'>
                              <h4 class='mx-auto text-center mt-5'>Current Discount Events</h4>
                              <div class='mt-2 d-flex container-fluid'>
                                    <form class="d-flex align-items-center w-100 search_form mx-auto" role="search" id="search_form">
                                          <button title='search coupon' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                      <g id="SVGRepo_iconCarrier">
                                                            <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                      </g>
                                                </svg>
                                          </button>

                                          <input id="search_coupon" class="form-control me-2" type="search" placeholder="Search by name" aria-label="Search">
                                    </form>
                              </div>
                              <div class="d-flex align-items-center mt-2 ms-3">
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
                              <div class="container-fluid overflow-x-auto">
                                    <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                          <thead id="table_head">
                                                <tr>
                                                      <th scope="col">#</th>
                                                      <th scope="col">Name</th>
                                                      <th scope="col">Discount Percentage</th>
                                                      <th scope="col">Period</th>
                                                      <th scope="col">Books Applied</th>
                                                </tr>
                                          </thead>
                                          <tbody id="table_body">
                                          </tbody>
                                    </table>
                              </div>
                              <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                                    <div class="d-flex ms-3">
                                          <p>Show&nbsp;</p>
                                          <p id="start_entry">
                                          </p>
                                          <p>&nbsp;to&nbsp;</p>
                                          <p id="end_entry">
                                          </p>
                                          <p>&nbsp;of&nbsp;</p>
                                          <p id="total_entries"></p>
                                          <p>&nbsp;entries</p>
                                    </div>
                                    <div class="group_buttonp me-3">
                                          <div class="btn-group d-flex" role="group">
                                                <button type="button" class="btn btn-outline-info" id="prev_button" onClick="changeList(false)">Previous</button>
                                                <button type="button" class="btn btn-info text-white" disabled id="list_offset">1</button>
                                                <button type="button" class="btn btn-outline-info" id="next_button" onClick="changeList(true)">Next</button>
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
            require_once __DIR__ . '/../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/javascript/admin/home/best_book.js"></script>
            <script src="/javascript/admin/home/best_category.js"></script>
            <script src="/javascript/admin/home/event_list.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/generate_color.js"></script>
      </body>

      </html>

<?php } ?>