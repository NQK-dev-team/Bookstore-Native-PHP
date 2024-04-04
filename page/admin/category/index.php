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
            <meta name="description" content="Manage book categories of NQK Bookstore">
            <title>Manage Book Categories</title>
            <link rel="stylesheet" href="/css/admin/category/category_list.css">
            <?php
            require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
            storeToken();
            ?>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class="container-fluid h-100 d-flex flex-column">
                        <h1 class='fs-2 mx-auto mt-3'>Book Category List</h1>
                        <div class="mb-2">
                              <button class="btn btn-success btn-sm" onclick="openAddModal()"><strong>+</strong> Add New Category</button>
                        </div>
                        <form class="d-flex align-items-center w-100 search_form" role="search" id="search_form">
                              <button title="search category" title='Search for category' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                    <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                          <g id="SVGRepo_iconCarrier">
                                                <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                          </g>
                                    </svg>
                              </button>

                              <input id="search_category" class="form-control me-2" type="search" placeholder="Search category by name" aria-label="Search for categories">
                        </form>
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
                        </div>
                        <div class="w-100 overflow-x-auto">
                              <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                    <thead>
                                          <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Action</th>
                                          </tr>
                                    </thead>
                                    <tbody id="table_body">
                                    </tbody>
                              </table>
                        </div>
                        <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                              <div class="d-flex">
                                    <p>Show&nbsp;</p>
                                    <p id="start_entry">1</p>
                                    <p>&nbsp;to&nbsp;</p>
                                    <p id="end_entry">10</p>
                                    <p>&nbsp;of&nbsp;</p>
                                    <p id="total_entries"></p>
                                    <p>&nbsp;entries</p>
                              </div>
                              <div class="group_button">
                                    <div class="btn-group d-flex" role="group">
                                          <button type="button" class="btn btn-outline-info" id="prev_button" onClick="changeList(false)" disabled>Previous</button>
                                          <button type="button" class="btn btn-info text-white" disabled id="list_offset">1</button>
                                          <button type="button" class="btn btn-outline-info" id="next_button" onClick="changeList(true)">Next</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="Error modal">
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
                  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="Confirm deletion">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Deletion</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to delete this category?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" onclick="deleteCategory()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="Confirm change modal">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Change</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to update this category?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" onclick="updateCategory()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="Confirm completion modal">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Completion</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to add this category?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" onclick="addCategory()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="addSuccessModal" tabindex="-1" aria-labelledby="Category added modal">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Category Added!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>New category has been added!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="updateSuccessModal" tabindex="-1" aria-labelledby="Category updated modal">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Category Updated!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Category has been updated!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="Category info modal">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5" id="inputModalTitle"></h2>
                                          <button type=" button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <div class="mb-3">
                                                <label for="categoryName" class="form-label fw-medium">Category Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                                <input type="text" class="form-control" id="categoryName" placeholder="Enter name">
                                          </div>
                                          <div class="mb-3">
                                                <label for="categoryDescription" class="form-label fw-medium">Category Description:</label>
                                                <textarea class="form-control" id="categoryDescription" rows="5" maxlength="500"></textarea>
                                          </div>
                                          <div>
                                                <button class='btn btn-sm btn-danger' onclick="resetForm()">Reset</button>
                                          </div>
                                    </div>
                                    <div class=" modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-success" id="inputModalConfirm">Save</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src=" /javascript/admin/menu_after_load.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/javascript/admin/category/category_list.js"></script>
            <script src="/javascript/admin/category/delete_category.js"></script>
            <script src="/javascript/admin/category/add_or_update_category.js"></script>
      </body>

      </html>

<?php } ?>