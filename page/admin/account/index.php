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

            $stmt = $conn->prepare('select name,dob,address,phone,email,imagePath,gender from appUser join admin on appUser.id = admin.id where appUser.id = ?');
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
            <meta name="description" content="Manage admin personal information of NQK Bookstore">
            <title>Manage Personal Info</title>
            <?php storeToken(); ?>
            <link rel="stylesheet" href="/css/admin/account/style.css">
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='d-flex w-100 h-100 flex-column'>
                        <div class="btn-group block mx-auto mt-4" role="group" aria-label="Radio toggle button group">
                              <input type="radio" class="btn-check" name="btnradio" id="btnradio1" checked>
                              <label class="btn btn-outline-primary" for="btnradio1">Personal Information</label>

                              <input type="radio" class="btn-check" name="btnradio" id="btnradio2">
                              <label class="btn btn-outline-primary" for="btnradio2">Change Password</label>
                        </div>
                        <form class='mt-4 block flex-grow-1 bg-white border border-1 rounded mx-auto mb-3 overflow-auto flex-column' id='passwordForm'>
                              <div>
                                    <h1 class='fs-3 ms-3 mt-3'>Change Password</h1>
                                    <hr class='mx-2'>
                              </div>
                              <div class='flex-column w-100 flex-grow-1 d-flex'>
                                    <label for="dummy_email" class='d-none'>Dummy Email (Should be hidden)</label>
                                    <input type="email" autocomplete="email" id="dummy_email" value="<?php echo $result['email']; ?>" disabled readonly class='d-none'>
                                    <div class="my-2 px-4">
                                          <label for="currentPasswordInput" class="form-label fw-medium">Current Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                          <input val='' type="password" class="form-control" id="currentPasswordInput" placeholder="Enter current password" autocomplete="current-password">
                                    </div>
                                    <div class="my-2 px-4">
                                          <label for="newPasswordInput" class="form-label fw-medium">New Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                          <input val='' type="password" class="form-control" id="newPasswordInput" placeholder="Enter new password" autocomplete="new-password" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters">
                                    </div>
                                    <div class="my-2 px-4">
                                          <label for="confirmPasswordInput" class="form-label fw-medium">Confirm New Password:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                          <input val='' type="password" class="form-control" id="confirmPasswordInput" placeholder="Confirm new password" autocomplete="new-password">
                                    </div>
                              </div>
                              <div>
                                    <hr class='mx-2'>
                                    <div class='d-flex justify-content-end me-2 mb-2'>
                                          <button class='btn btn-secondary me-2' type='reset'>Reset</button>
                                          <button class='btn btn-primary ms-2' type='submit' onclick="saveChange()">Save Changes</button>
                                    </div>
                              </div>
                        </form>
                        <form class='mt-4 block flex-grow-1 bg-white border border-1 rounded mx-auto mb-3 overflow-auto flex-column' id='personalInfoForm'>
                              <div>
                                    <h1 class='fs-3 ms-3 mt-3'>Personal Information</h1>
                                    <hr class='mx-2'>
                              </div>
                              <div class='w-100 flex-grow-1 p-2 flex-column flex-lg-row d-flex'>
                                    <div class="col-lg-5 col-12">
                                          <div class='w-100 d-flex flex-column h-100 justify-content-center'>
                                                <img class='custom_image w-100 mx-auto' id="userImage" alt="user image" data-initial-src="<?php if ($result['imagePath'])
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
                                    <div class="col-lg-7 col-12">
                                          <div class='w-100 d-flex flex-column h-100'>
                                                <div class="mt-auto mb-2 px-lg-5 px-3">
                                                      <label for="nameInput" class="form-label">Name:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input autocomplete="name" type="text" class="form-control" id="nameInput" data-initial-value="<?php echo $result['name']; ?>" placeholder="Enter name">
                                                </div>
                                                <div class="my-2 px-lg-5 px-3">
                                                      <label for="emailInput" class="form-label">Email:</label>
                                                      <input readonly autocomplete="email" type="email" class="form-control" id="emailInput" data-initial-value="<?php echo $result['email']; ?>" disabled>
                                                </div>
                                                <div class="my-2 px-lg-5 px-3">
                                                      <label for="phoneInput" class="form-label">Phone:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input autocomplete="tel" type="tel" class="form-control" id="phoneInput" data-initial-value="<?php echo $result['phone']; ?>" placeholder="Enter phone number">
                                                </div>
                                                <div class="my-2 px-lg-5 px-3">
                                                      <label for="dobInput" class="form-label">Date Of Birth:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input autocomplete="bday" type="date" class="form-control" id="dobInput" data-initial-value="<?php echo $result['dob']; ?>">
                                                </div>
                                                <div class="my-2 px-lg-5 px-3">
                                                      <label for="genderInput" class="form-label">Gender:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <select autocomplete="sex" class="form-select" aria-label="Select gender" id='genderInput' data-initial-value="<?php echo $result['gender']; ?>">
                                                            <option value=null>Choose your gender</option>
                                                            <option <?php if ($result['gender'] === 'M') echo 'selected'; ?> value="M">Male</option>
                                                            <option <?php if ($result['gender'] === 'F') echo 'selected'; ?> value="F">Female</option>
                                                            <option <?php if ($result['gender'] === 'O') echo 'selected'; ?> value="O">Other</option>
                                                      </select>
                                                </div>
                                                <div class="mb-auto mt-2 px-lg-5 px-3">
                                                      <label for="addressInput" class="form-label">Address:<span class='fw-bold text-danger'>&nbsp;*</span></label>
                                                      <input autocomplete="off" type="text" class="form-control" id="addressInput" data-initial-value="<?php echo $result['address']; ?>" placeholder="Enter address">
                                                </div>
                                          </div>
                                    </div>
                              </div>
                              <div>
                                    <hr class='mx-2'>
                                    <div class='d-flex justify-content-end me-2 mb-2'>
                                          <button class='btn btn-secondary me-2' type='button' onclick="resetForm()">Reset</button>
                                          <button class='btn btn-primary ms-2' type='submit' onclick="saveChange()">Save Changes</button>
                                    </div>
                              </div>
                        </form>
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
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/javascript/admin/account/script.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/tool/js/dob_checker.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
      </body>

      </html>

<?php } ?>