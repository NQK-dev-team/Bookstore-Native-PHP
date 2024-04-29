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
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';
      require_once __DIR__ . '/../../../tool/php/check_https.php';

      try {
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }

            $stmt = $conn->prepare('select name,dob,address,phone,email,imagePath,gender from appUser join customer on appUser.id = customer.id where appUser.id = ?');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $stmt->bind_param('s', $_SESSION['id']);
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                  http_response_code(404);
                  require_once __DIR__ . '/../../../error/404.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $result = $result->fetch_assoc();
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
            <meta name="description" content="Manage customer personal information of NQK Bookstore">
            <title>Manage Personal Info</title>
            <?php storeToken(); ?>
            <link rel="stylesheet" href="/css/customer/account/style.css">
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div class='d-flex w-100 h-100 flex-column'>
                        <div class='block bg-white border border-3 rounded m-auto d-flex flex-column container-fluid'>
                              <div>
                                    <h1 class='mb-3 mt-3'>My account</h1>
                                    <div class='d-flex overflow-x-auto'>
                                          <p class='mb-2 pointer hover-tab selected text-nowrap' id='select-personal-info' name='select-tab'>Personal Information</p>
                                          <p class='mb-2 ms-4 pointer hover-tab text-nowrap' id='select-purchases' name='select-tab'>Purchases</p>
                                          <p class='mb-2 ms-4 pointer hover-tab text-nowrap' id='select-password' name='select-tab'>Change Password</p>
                                          <p class='mb-2 ms-4 me-1 pointer hover-tab text-nowrap' id='select-other' name='select-tab'>Other</p>
                                    </div>
                              </div>
                              <hr>
                              <form class='flex-column flex-grow-1' id='personalInfoForm'>
                                    <div class='w-100 flex-grow-1 row m-0'>
                                          <div class="col-lg-5 col-12 p-0">
                                                <div class='w-100 d-flex flex-column h-100 justify-content-center'>
                                                      <img class='custom_image w-100 mx-auto border border-2 rounded' id="userImage" alt="user image" data-initial-src="<?php if ($result['imagePath'])
                                                                                                                                                                              echo (isSecure() ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/data/user/customer/" . normalizeURL(rawurlencode($result['imagePath']));
                                                                                                                                                                        else {
                                                                                                                                                                              if ($result['gender'] === 'M')
                                                                                                                                                                                    echo '/image/default_male.jpeg';
                                                                                                                                                                              else if ($result['gender'] === 'F')
                                                                                                                                                                                    echo '/image/default_female.jpg';
                                                                                                                                                                              else if ($result['gender'] === 'O')
                                                                                                                                                                                    echo '/image/default_other.png';
                                                                                                                                                                        } ?>">
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
                                          <div class="col-lg-7 col-12 p-0">
                                                <div class='w-100 d-flex flex-column h-100'>
                                                      <div class="mt-auto mb-2 px-lg-5 px-3">
                                                            <label for="nameInput" class="form-label fw-medium">Name:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                            <input autocomplete="name" type="text" class="form-control" id="nameInput" data-initial-value="<?php echo $result['name']; ?>" placeholder="Enter name">
                                                      </div>
                                                      <div class="my-2 px-lg-5 px-3">
                                                            <label for="emailInput" class="form-label fw-medium">Email:</label>
                                                            <input readonly autocomplete="email" type="email" class="form-control" id="emailInput" data-initial-value="<?php echo $result['email']; ?>" disabled placeholder="Enter email address">
                                                      </div>
                                                      <div class="my-2 px-lg-5 px-3">
                                                            <label for="phoneInput" class="form-label fw-medium">Phone:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                            <input maxlength="10" autocomplete="tel" type="tel" class="form-control" id="phoneInput" data-initial-value="<?php echo $result['phone']; ?>" placeholder="Enter phone number">
                                                      </div>
                                                      <div class="my-2 px-lg-5 px-3">
                                                            <label for="dobInput" class="form-label fw-medium">Date Of Birth:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                            <input autocomplete="bday" type="date" class="form-control" id="dobInput" data-initial-value="<?php echo $result['dob']; ?>">
                                                      </div>
                                                      <div class="my-2 px-lg-5 px-3">
                                                            <label for="genderInput" class="form-label fw-medium">Gender:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                            <select autocomplete="sex" class="form-select" aria-label="Select gender" id='genderInput' data-initial-value="<?php echo $result['gender']; ?>">
                                                                  <option value=null>Choose your gender</option>
                                                                  <option <?php if ($result['gender'] === 'M') echo 'selected'; ?> value="M">Male</option>
                                                                  <option <?php if ($result['gender'] === 'F') echo 'selected'; ?> value="F">Female</option>
                                                                  <option <?php if ($result['gender'] === 'O') echo 'selected'; ?> value="O">Other</option>
                                                            </select>
                                                      </div>
                                                      <div class="mb-auto mt-2 px-lg-5 px-3">
                                                            <label for="addressInput" class="form-label fw-medium">Address:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                            <input autocomplete="off" type="text" class="form-control" id="addressInput" data-initial-value="<?php echo $result['address']; ?>" placeholder="Enter address">
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='mt-5'></div>
                                    <hr class='mt-auto'>
                                    <div class='d-flex justify-content-end pb-4 mb-5 pb-lg-0'>
                                          <button class='btn btn-secondary me-2' type='button' onclick="resetForm()">Reset</button>
                                          <button class='btn btn-primary ms-2' type='submit' onclick="saveChange()">Save Changes</button>
                                    </div>
                              </form>
                              <div id='historyPurchase' class='flex-column'>
                                    <p class='fw-medium'>Current Accummulated Points:&nbsp;<span id="current_point" class='text-success'></span></p>
                                    <div>
                                          <form class="d-flex align-items-center w-100 search_form mt-2" role="search" id="search_order_form">
                                                <button title='search order' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                                      <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                  <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                            </g>
                                                      </svg>
                                                </button>

                                                <input id="search_order" class="form-control me-2" type="search" placeholder="Search by order code" aria-label="Search">
                                          </form>

                                          <label for="orderDateInput" class="form-label fw-medium mt-3">Order Date:</label>
                                          <input autocomplete="off" type="date" class="form-control search_form" id="orderDateInput" onchange='findOrder()'>
                                    </div>
                                    <div class='w-100 flex-grow-1 my-4 overflow-x-auto hideBrowserScrollbar'>
                                          <table class="table table-hover border border-2 table-bordered w-100 rounded mb-5">
                                                <thead>
                                                      <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Order Code</th>
                                                            <th scope="col">Purchase Time</th>
                                                            <th scope="col">Total Price</th>
                                                            <th scope="col">Total Discount</th>
                                                            <th scope="col">Book</th>
                                                            <th scope="col">Action</th>
                                                      </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                              <form class='flex-column flex-grow-1' id='passwordForm'>
                                    <div class='flex-column w-100 d-flex'>
                                          <label for="dummy_email" class='d-none'>Dummy Email (Should be hidden)</label>
                                          <input type="email" autocomplete="email" id="dummy_email" value="<?php echo $result['email']; ?>" disabled readonly class='d-none'>
                                          <div class="my-2">
                                                <label for="currentPasswordInput" class="form-label fw-medium">Current Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                <input val='' type="password" class="form-control" id="currentPasswordInput" placeholder="Enter current password" autocomplete="current-password">
                                          </div>
                                          <div class="my-2">
                                                <label for="newPasswordInput" class="form-label fw-medium">New Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                <input val='' type="password" class="form-control" id="newPasswordInput" placeholder="Enter new password" autocomplete="new-password" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters">
                                          </div>
                                          <div class="my-2">
                                                <label for="confirmPasswordInput" class="form-label fw-medium">Confirm New Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                <input val='' type="password" class="form-control" id="confirmPasswordInput" placeholder="Confirm new password" autocomplete="new-password">
                                          </div>
                                    </div>
                                    <div class='mt-5'></div>
                                    <hr class='mt-auto'>
                                    <div class='d-flex justify-content-end pb-4 mb-5 pb-lg-0'>
                                          <button class='btn btn-secondary me-2' type='reset'>Reset</button>
                                          <button class='btn btn-primary ms-2' type='submit' onclick="saveChange()">Save Changes</button>
                                    </div>
                              </form>
                              <div class='flex-column pb-5' id='otherTab'>
                                    <div>
                                          <p class='fw-medium mb-0'>Deactivate Account</p>
                                          <p>(You account will be disabled. To activate it, simply login)</p>
                                          <button class='btn btn-sm btn-secondary' onclick="$('#deactivateModal').modal('show');">Deactivate Account</button>
                                    </div>
                                    <div class='mt-5'>
                                          <p class='fw-medium mb-0'>Delete Account</p>
                                          <p>(Your account will be deleted automatically after 14 days. Before this, you can login and the process will be cancelled)</p>
                                          <button class='btn btn-sm btn-danger' onclick="$('#deleteModal').modal('show');">Delete Account</button>
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
                  <div class="modal fade" id="confirmPersonalModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Change</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to update your personal information?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="changePersonalInfo()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Change</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to update your password?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="changePassword()">Confirm</button>
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
                                          <p>Changes applied successfully!</p>
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
                                          <p>Are you sure you want to disable your account?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="deactivateAccount()">Confirm</button>
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
                                          <p>Are you sure you want to delete your account?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="deleteAccount()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-xl-custom modal-dialog-scrollable">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <div class='d-flex'>
                                                <h2 class="modal-title fs-5">Order:&nbsp;</h2>
                                                <h2 class="modal-title fs-5 fw-normal" id='orderID'></h2>
                                          </div>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <div class='d-flex'>
                                                <p class='fw-medium'>Order Time:&nbsp;</p>
                                                <p id='orderTime'></p>
                                          </div>
                                          <div class='d-flex'>
                                                <p class='fw-medium'>Total Discount:&nbsp;</p>
                                                <p id='orderDiscount'></p>
                                          </div>
                                          <div class='d-flex'>
                                                <p class='fw-medium'>Total Price:&nbsp;</p>
                                                <p id='orderPrice'></p>
                                          </div>

                                          <div class='mt-4'>
                                                <div class='flex-column' id='fileCopyDisplay'>
                                                      <h5>E-books</h5>
                                                      <div class="w-100 overflow-x-auto">
                                                            <table class="table table-hover border border-2 table-bordered w-100">
                                                                  <thead>
                                                                        <tr>
                                                                              <th scope="col">#</th>
                                                                              <th scope="col">Image</th>
                                                                              <th scope="col">Name</th>
                                                                              <th scope="col">Edition</th>
                                                                              <th scope="col">ISBN-13</th>
                                                                              <th scope="col">Author</th>
                                                                              <th scope="col">Category</th>
                                                                              <th scope="col">Publisher</th>
                                                                              <th scope="col">Description</th>
                                                                              <th scope="col">Rating</th>
                                                                              <th scope="col">Price</th>
                                                                              <th scope="col">Action</th>
                                                                        </tr>
                                                                  </thead>
                                                                  <tbody id="file_table_body">
                                                                  </tbody>
                                                            </table>
                                                      </div>
                                                </div>
                                                <div class='flex-column mt-3' id='physicalCopyDisplay'>
                                                      <h5>Hardcovers</h5>
                                                      <p>
                                                            <span class='fw-medium'>Delivery Address:&nbsp;</span>
                                                            <span id='physicalDestination'></span>
                                                      </p>
                                                      <div class="w-100 overflow-x-auto">
                                                            <table class="table table-hover border border-2 table-bordered w-100">
                                                                  <thead>
                                                                        <tr>
                                                                              <th scope="col">#</th>
                                                                              <th scope="col">Image</th>
                                                                              <th scope="col">Name</th>
                                                                              <th scope="col">Edition</th>
                                                                              <th scope="col">ISBN-13</th>
                                                                              <th scope="col">Author</th>
                                                                              <th scope="col">Category</th>
                                                                              <th scope="col">Publisher</th>
                                                                              <th scope="col">Description</th>
                                                                              <th scope="col">Rating</th>
                                                                              <th scope="col">Price</th>
                                                                              <th scope="col">Ammount</th>
                                                                        </tr>
                                                                  </thead>
                                                                  <tbody id="physical_table_body">
                                                                  </tbody>
                                                            </table>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/tool/js/dob_checker.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/javascript/customer/account/script.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
      </body>

      </html>

<?php } ?>