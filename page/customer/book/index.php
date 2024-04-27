<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <link rel="stylesheet" href="/css/customer/book/book-list.css">
            <meta name="page creator" content="Anh Khoa, Nghia Duong">
            <meta name="description" content="Browse book list of NQK bookstore">
            <title>Browse Books</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div class="container-xxl my-3 px-1 px-xl-3">
                        <div class='d-flex'>
                              <div class='d-none d-xl-block panel border border-2 me-4 bg-white p-3 rounded'>
                                    <div>
                                          <h4>Category</h4>
                                          <input onchange='fetchCategoryList()' id='categorySearch' class="form-control" type="search" placeholder="Search" aria-label="Search by categories">
                                          <div class='ps-2 mt-3' id='categoryList'>
                                          </div>
                                    </div>
                                    <div class='mt-4'>
                                          <h4>Author</h4>
                                          <input onchange='fetchAuthorList()' id='authorSearch' class="form-control" type="search" placeholder="Search" aria-label="Search by authors">
                                          <div class='ps-2 mt-3' id='authorList'>
                                          </div>
                                    </div>
                                    <div class='mt-4'>
                                          <h4>Publisher</h4>
                                          <input onchange='fetchPublisherList()' id='publisherSearch' class="form-control" type="search" placeholder="Search" aria-label="Search by publishers">
                                          <div class='ps-2 mt-3' id='publisherList'>
                                          </div>
                                    </div>
                              </div>
                              <div class='flex-grow-1 border border-2 bg-white d-flex flex-column px-1 px-sm-2 rounded' id='listContainer'>
                                    <form class="d-flex align-items-center w-100 search_form mt-3" role="search" id="search_form">
                                          <button title='submit search form' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                      <g id="SVGRepo_iconCarrier">
                                                            <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                      </g>
                                                </svg>
                                          </button>

                                          <input id="search_book" class="form-control" type="search" placeholder="Search book by name" aria-label="Search for books">
                                    </form>
                                    <div class='d-flex mt-3'>
                                          <div class='d-sm-flex'>
                                                <div>
                                                      <select onchange="fetchBook()" id='listOption' class="form-select pointer" aria-label="Select listing option">
                                                            <option value="1" selected>Default Listing</option>
                                                            <option value="2">On Sale</option>
                                                            <option value="3">This Week Best Sellers</option>
                                                            <option value="4">Price: Low to High</option>
                                                            <option value="5">Price: High to Low</option>
                                                      </select>
                                                </div>
                                                <div class='ms-sm-3 mt-3 mt-sm-0'>
                                                      <select id='listLimit' class="form-select pointer" aria-label="Select number of books per page">
                                                            <option value="12" selected>12 books</option>
                                                            <option value="24">24 books</option>
                                                            <option value="48">48 books</option>
                                                      </select>
                                                </div>
                                          </div>
                                          <div class='d-block d-xl-none ms-3'>
                                                <button type="button" class="btn border border-1 border-secondary" data-bs-toggle="modal" data-bs-target="#filterModal"><svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                  <path d="M14 20.5H10C9.80189 20.4974 9.61263 20.4176 9.47253 20.2775C9.33244 20.1374 9.25259 19.9481 9.25 19.75V12L3.9 4.69C3.81544 4.58007 3.76395 4.44832 3.75155 4.31018C3.73915 4.17204 3.76636 4.03323 3.83 3.91C3.89375 3.78712 3.98984 3.68399 4.10792 3.61173C4.226 3.53947 4.36157 3.50084 4.5 3.5H19.5C19.6384 3.50084 19.774 3.53947 19.8921 3.61173C20.0101 3.68399 20.1062 3.78712 20.17 3.91C20.2336 4.03323 20.2608 4.17204 20.2484 4.31018C20.236 4.44832 20.1846 4.58007 20.1 4.69L14.75 12V19.75C14.7474 19.9481 14.6676 20.1374 14.5275 20.2775C14.3874 20.4176 14.1981 20.4974 14 20.5ZM10.75 19H13.25V11.75C13.2492 11.5907 13.302 11.4357 13.4 11.31L18 5H6L10.62 11.31C10.718 11.4357 10.7708 11.5907 10.77 11.75L10.75 19Z" fill="#000000"></path>
                                                            </g>
                                                      </svg><span class='customDisplay'>&nbsp;Filter</span></button>
                                          </div>
                                          <div class="btn-group d-none d-xl-inline-flex ms-3">
                                                <button onclick="adJustOffset(false)" name="previous" type="button" class="btn btn-light fw-medium border border-1 border-secondary">&lt;</button>
                                                <button type="button" class="btn btn-light fw-medium border border-1 border-secondary" disabled name="offset"></button>
                                                <button onclick="adJustOffset(true)" name="next" type="button" class="btn btn-light fw-medium border border-1 border-secondary">&gt;</button>
                                          </div>
                                    </div>
                                    <hr>
                                    <div id='bookList' class='d-flex flex-column'></div>
                                    <div class='mx-auto mb-3 mt-auto'>
                                          <div class="btn-group mt-3">
                                                <button onclick="adJustOffset(false)" name="previous" type="button" class="btn btn-light fw-medium border border-1 border-secondary">&lt;</button>
                                                <button type="button" class="btn btn-light fw-medium border border-1 border-secondary" disabled name="offset"></button>
                                                <button onclick="adJustOffset(true)" name="next" type="button" class="btn btn-light fw-medium border border-1 border-secondary">&gt;</button>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class='container-xxl my-3 px-1 px-xl-3'>
                        <form class="bg-white rounded d-flex flex-column border border-2 p-2" id='newBookForm'>
                              <h3 class='mx-auto mt-2'>Request new book</h3>
                              <small class='mx-auto text-center'>Is there a book you want to add to our store?</small>
                              <small class='mx-auto text-center'>Enter the name and author below and we will get it soon...</small>
                              <div class='d-flex flex-column flex-md-row p-3'>
                                    <div class='flex-grow-1'>
                                          <label for="book_name" class="form-label">Book Name:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                          <input type="text" class="form-control" id="book_name" placeholder="Enter book name">
                                    </div>
                                    <div class='ms-md-3 mt-3 mt-md-0 flex-grow-1'>
                                          <label for="book_author" class="form-label">Author:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                          <input type="text" class="form-control" id="book_author" placeholder="Enter author name">
                                          <small class="form-text text-muted">You can enter multiple authors with each seperated by comma</small>
                                    </div>
                              </div>
                              <div class='mb-3 mx-auto'>
                                    <button type='submit' class='btn btn-light border border-1 border-secondary'>Submit</button>
                              </div>
                        </form>
                  </div>
                  <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <div>
                                                <div>
                                                      <h4>Category</h4>
                                                      <input onchange='fetchCategoryList()' id='categorySearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by categories">
                                                      <div class='ps-2 mt-3' id='categoryListModal'>
                                                      </div>
                                                </div>
                                                <div class='mt-4'>
                                                      <h4>Author</h4>
                                                      <input onchange='fetchAuthorList()' id='authorSearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by authors">
                                                      <div class='ps-2 mt-3' id='authorListModal'>
                                                      </div>
                                                </div>
                                                <div class='mt-4'>
                                                      <h4>Publisher</h4>
                                                      <input onchange='fetchPublisherList()' id='publisherSearchModal' class="form-control" type="search" placeholder="Search" aria-label="Search by publishers">
                                                      <div class='ps-2 mt-3' id='publisherListModal'>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class=" modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Error</h2>
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
                  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Success</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Your request was successfully sent, thank you for your contribution!</p>
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
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
            <script src="/javascript/customer/book/book-list.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/input_validity.js"></script>
      </body>

      </html>

<?php } ?>