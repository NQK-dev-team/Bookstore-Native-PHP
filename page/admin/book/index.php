<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../../error/403.php';
} else {
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
                              <button class="btn btn-success"><strong>+</strong> Add New Book</button>
                        </div>
                        <div class="mt-3 d-flex align-items-center">
                              <p class="mb-0 me-2">Show</p>
                              <div>
                                    <select id="entry_select" class="form-select" aria-label="Entry selection">
                                          <option value=10 selected>10</option>
                                          <option value=25>25</option>
                                          <option value=50>50</option>
                                          <option value=100>100</option>
                                    </select>
                              </div>
                              <p class="mb-0 ms-2">entries</p>
                        </div>
                        <div class="w-100 overflow-x-auto">
                              <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                    <thead>
                                          <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Image</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Author</th>
                                                <th scope="col">Category</th>
                                                <th scope="col">Publisher</th>
                                                <th scope="col">Publish Date</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Action</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          <tr>
                                                <td class="col-1 align-middle">1</td>
                                                <td class="col-2"><img src="https://templates.iqonic.design/booksto/html/images/browse-books/01.jpg" class="book_image"></img></td>
                                                <td class="col-3 align-middle">1</td>
                                                <td class="col-1 align-middle">1</td>
                                                <td class="col-1 align-middle">1</td>
                                                <td class="col-1 align-middle">1</td>
                                                <td class="col-1 align-middle">1</td>
                                                <td class="col-1 align-middle">$399</td>
                                                <td class="col-1 align-middle">
                                                      <div class="d-flex flex-lg-row flex-column">
                                                            <button class="btn btn-info">
                                                                  <svg width="20px" height="20px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                            </button>
                                                            <button class="btn btn-danger ms-lg-2 mt-2 mt-lg-0">
                                                                  <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M10 12L14 16M14 12L10 16M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                            </button>
                                                      </div>
                                                </td>
                                          </tr>
                                    </tbody>
                              </table>
                        </div>
                        <div class="w-100 d-flex justify-content-end mb-4 mt-2">
                              <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-info" id="prev_button" onClick="changeList(false)">Previous</button>
                                    <button type="button" class="btn btn-info text-white" disabled id="list_offset">1</button>
                                    <button type="button" class="btn btn-outline-info" id="next_button" onClick="changeList(true)">Next</button>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/javascript/admin/book/book_list.js"></script>
            <script src="/javascript/admin/book/add_book.js"></script>
            <script src="/javascript/admin/book/edit_book.js"></script>
      </body>

      </html>

<?php } ?>