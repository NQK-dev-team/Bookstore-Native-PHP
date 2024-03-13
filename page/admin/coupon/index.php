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
            <meta name="description" content="Manage discount coupons of NQK Bookstore">
            <title>Manage Discount Coupons</title>
            <link rel="stylesheet" href="/css/admin/coupon/discount_list.css">
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
                        <h1 class='fs-2 mx-auto mt-3'>Discount Coupon List</h1>
                        <div class='mt-2 d-flex flex-column flex-lg-row align-items-center'>
                              <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1" role="search" id="search_form">
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
                              <div class="mx-auto mx-lg-0 ms-lg-2 order-1 order-lg-2">
                                    <button class="btn btn-success btn-sm" onclick="openAddModal()"><strong>+</strong> Add New Coupon</button>
                              </div>
                        </div>
                        <div class="mt-2">
                              <div class="d-flex align-items-center">
                                    <p class="mb-0 me-2">Coupon Type</p>
                                    <div>
                                          <select class="form-select pointer" aria-label="Select coupon type" id='couponSelect' onchange="selectEntry()">
                                                <option value="1" selected>Event</option>
                                                <option value="2">Point</option>
                                                <option value="3">Referrer</option>
                                          </select>
                                    </div>
                              </div>
                              <div class="d-flex align-items-center mt-2">
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
                                          <label class="form-check-label text-success" for="flexSwitchCheckDefault" id="switch_label">Choose active coupons</label>
                                          <input title='Coupon status' class="form-check-input pointer" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked onchange="updateSwitchLabel()">
                                    </div>
                              </div>
                        </div>
                        <div class="w-100 overflow-x-auto">
                              <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                    <thead id="table_head">
                                          <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Discount Percentage</th>
                                                <th scope="col">Period</th>
                                                <th scope="col">Books Applied</th>
                                                <th scope="col">Status</th>
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
                  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Deletion</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to delete this coupon?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" onclick="deleteCoupon()">Confirm</button>
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
                                          <p>Are you sure you want to deactivate this coupon?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" onclick="deactivateCoupon()">Confirm</button>
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
                                          <p>Are you sure you want to activate this coupon?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" onclick="activateCoupon()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Add a coupon</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <form id="addCouponForm">
                                          </form>
                                          <div class='mt-3'>
                                                <button class="btn btn-danger btn-sm" type='reset' form='addCouponForm' onclick="clearForm()">Reset</button>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="submit" class="btn btn-success" onclick="clearAllCustomValidity()" form="addCouponForm">Save</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Edit coupon</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <form id="updateCouponForm">
                                          </form>
                                          <div class='mt-3'>
                                                <button class="btn btn-danger btn-sm" type='reset' form='updateCouponForm' onclick="clearForm()">Reset</button>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="submit" class="btn btn-success" onclick="clearAllCustomValidity()" form="updateCouponForm">Save</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="chooseBookModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Select Books</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <div class='w-100 mt-2 mb-3'>
                                                <div>
                                                      <label class="form-label" for='searchBookInput'>Search Books:</label>
                                                      <form id="book_search_form" class="d-flex align-items-center w-100" role="search">
                                                            <button title='search book' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                                  <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                                        </g>
                                                                  </svg>
                                                            </button>
                                                            <input placeholder='Search by name' class="form-control search_book" type='text' id='searchBookInput'></input>
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
                                                      <div class='d-flex mt-2'>
                                                            <p class="mb-0">Selected:&nbsp;</p>
                                                            <strong id='totalSelected'></strong>
                                                      </div>
                                                </div>
                                          </div>
                                          <div class="w-100 overflow-auto" id='book_list'>
                                                <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                                      <thead>
                                                            <tr>
                                                                  <th scope="col" class="text-center"><input title='Select all books' type='checkbox' class='pointer' id='checkAll' onclick="selectAllBook(event)"></th>
                                                                  <th scope="col">#</th>
                                                                  <th scope="col">Name</th>
                                                                  <th scope="col">Edition</th>
                                                                  <th scope="col">Category</th>
                                                                  <th scope="col"></th>
                                                            </tr>
                                                      </thead>
                                                      <tbody id="book_table_body">
                                                      </tbody>
                                                </table>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between align-items-center">
                                                <div class="d-flex align-middle">
                                                      <p>Show&nbsp;</p>
                                                      <p id="book_start_entry"></p>
                                                      <p>&nbsp;to&nbsp;</p>
                                                      <p id="book_end_entry"></p>
                                                      <p>&nbsp;of&nbsp;</p>
                                                      <p id="total_book_entries"></p>
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
                        </div>
                  </div>
                  <div class="modal fade" id="confirmAddModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Changes</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to create this coupon?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="addCoupon()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="successAddModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Success</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>New discount coupon added!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Changes</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to update this coupon?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="updateCoupon()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="successUpdateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Changes Saved</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Discount coupon updated!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="dataAnomalies" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Warning</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Changing books applied for this coupon can cause incorrect data presentation when performing statistical analysis, do you really want to do this?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id='anomaliesConfirm'>Confirm</button>
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
            <script src="/tool/js/input_validity.js"></script>
            <script src="/javascript/admin/coupon/discount_list.js"></script>
            <script src="/javascript/admin/coupon/book_list.js"></script>
            <script src="/javascript/admin/coupon/add_discount.js"></script>
            <script src="/javascript/admin/coupon/toggle_status.js"></script>
            <script src="/javascript/admin/coupon/delete_discount.js"></script>
            <script src="/javascript/admin/coupon/update_discount.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
      </body>

      </html>

<?php } ?>