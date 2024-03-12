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
            <meta name="description" content="Add a book to NQK Bookstore">
            <title>Add Book</title>
            <link rel="stylesheet" href="/css/admin/book/book_detail.css">
            <?php storeToken(); ?>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='w-100 h-100 d-flex'>
                        <form onsubmit="confirmSubmitForm(event)" class='position-relative border border-1 rounded border-dark custom_container m-auto bg-white d-flex flex-column overflow-y-auto overflow-x-hidden'>
                              <h1 class='ms-xl-3 mt-2 mx-auto'>Add Book</h1>
                              <div class="ms-auto me-3 mt-xl-3 mb-3 mb-xl-2 mt-5 order-xl-1 order-2 button_group align-self-xl-end">
                                    <button class="btn btn-secondary ms-1" onclick="resetForm()" type='button'>Reset</button>
                                    <button class="btn btn-success me-1" type='submit' onclick="clearAllCustomValidity()">Save</button>
                              </div>
                              <div class='row flex-grow-1 order-xl-2 order-1'>
                                    <div class="col-xl-5 col-12">
                                          <div class='d-flex flex-column align-items-center w-100 h-100 ps-xl-5 px-3'>
                                                <div class="mt-xl-auto my-2 mt-3">
                                                      <label for="bookNameInput" class="form-label">Book Name:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="text" class="form-control fs-4" id="bookNameInput">
                                                </div>
                                                <div class="mb-auto my-2 d-flex flex-column w-100 align-items-center">
                                                      <p class='mb-0 fw-medium' id='bookImagePlaceHolder'>Book image here!<span class='fw-bold text-danger'>&nbsp;*</span></p>
                                                      <img class='custom_image w-100 d-none' id="bookImage" alt="book image">
                                                      </img>
                                                      <label class='btn btn-sm btn-light border border-dark mt-3 mx-auto'>
                                                            <input accept='image/jpeg,image/png' id="imageInput" type='file' class='d-none' onchange="setNewImage(event)"></input>
                                                            Browse
                                                      </label>
                                                      <p id="imageFileName" class='mx-auto mt-2'></p>
                                                      <div class='mx-auto text-danger d-none' id="imgeFileError">
                                                            <p class='text-danger'><i class="bi bi-exclamation-triangle"></i>&nbsp;</p>
                                                            <p class='text-danger' id='imgeFileErrorMessage'></p>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                          <div class='d-flex flex-column ps-xl-5 w-100 h-100'>
                                                <div class="mt-auto mb-2 px-xl-5 px-3">
                                                      <label for="editionInput" class="form-label">Edition:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="number" class="form-control" id="editionInput">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="isbnInput" class="form-label">ISBN-13:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="text" class="form-control" id="isbnInput">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="ageInput" class="form-label">Age Restriction:</label>
                                                      <input type="number" class="form-control" id="ageInput">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="authorInput" class="form-label">Author:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="text" class="form-control" id="authorInput">
                                                      <small class="form-text text-muted">You can enter multiple authors with each seperated by comma</small>
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="categoryInput" class="form-label">Category:</label>
                                                      <textarea rows="4" readonly onclick="openCategoryModal()" class="form-control pointer" id="categoryInput"></textarea>
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="publisherInput" class="form-label">Publisher:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="text" class="form-control" id="publisherInput">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="publishDateInput" class="form-label">Publish Date:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input type="date" class="form-control" id="publishDateInput">
                                                </div>
                                                <div class="my-2 px-xl-5 px-3">
                                                      <label for="descriptionInput" class="form-label">Description:</label>
                                                      <textarea rows="5" class="form-control" id="descriptionInput" maxlength='2000'></textarea>
                                                </div>
                                                <div class="my-2 px-xl-5 px-3 d-flex flex-md-row flex-column row">
                                                      <div class='col'>
                                                            <label for="physicalPriceInput" class="form-label">Physical Copy Price ($):</label>
                                                            <input step="any" type="number" class="form-control" id="physicalPriceInput" placeholder="Enter price">
                                                      </div>
                                                      <div class="ms-md-5 mt-2 mt-md-0 col">
                                                            <label for="inStockInput" class="form-label">In Stock:</label>
                                                            <input type="number" class="form-control" id="inStockInput" placeholder="Enter number">
                                                      </div>
                                                </div>
                                                <div class="mb-auto mt-2 px-xl-5 px-3 d-flex flex-md-row flex-column row">
                                                      <div class='col mb-3'>
                                                            <label for="filePriceInput" class="form-label">E-book Price ($):</label>
                                                            <input step="any" type="number" class="form-control" id="filePriceInput" placeholder="Enter price">
                                                      </div>
                                                      <div class="ms-md-5 mt-2 mt-md-0 col mb-3">
                                                            <div class="d-flex flex-column h-100">
                                                                  <span class="form-label">
                                                                        E-book file:
                                                                  </span>
                                                                  <div class="d-flex align-items-center mt-auto" id="btn_grp">
                                                                        <label class='btn btn-sm btn-light border border-dark'>
                                                                              <input type="file" class="form-control d-none" id="filePathInput" accept='.pdf' onchange="setNewFile(event)">
                                                                              Browse
                                                                        </label>
                                                                  </div>
                                                                  <p class="mt-1" id="pdfFileName"></p>
                                                                  <p id="pdfFileError1" class='text-danger mt-2 d-none'><i class="bi bi-exclamation-triangle"></i>&nbsp;Invalid PDF file!</p>
                                                                  <p id="pdfFileError2" class='text-danger mt-2 d-none'><i class="bi bi-exclamation-triangle"></i>&nbsp;Only submit 1 PDF file!</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </form>
                  </div>
                  <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Select category</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <div class='w-100 mt-2 mb-4'>
                                                <label class="form-label" for='searchCategoryInput'>Search category:</label>
                                                <form id="category_search_form" class="d-flex align-items-center w-100 search_form mx-auto" role="search">
                                                      <button aria-label="Search for category" class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                            <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                  <g id="SVGRepo_iconCarrier">
                                                                        <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                                  </g>
                                                            </svg>
                                                      </button>
                                                      <input class="form-control search_category" type='text' id='searchCategoryInput'></input>
                                                </form>
                                          </div>
                                          <div class="w-100 overflow-y-auto" id='category_list'>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Save</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to add this book?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="submitForm()">Confirm</button>
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
                  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Success!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>New book added!</p>
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
            <script src="/javascript/admin/book/add_book.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
      </body>

      </html>

<?php } ?>