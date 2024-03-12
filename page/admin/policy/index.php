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
      
      require_once __DIR__ . '/../../../tool/php/anti_csrf.php';

      try {
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }

            $stmt = $conn->prepare('select * from pointConfig');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }

            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $percentage = $stmt->get_result()->fetch_assoc()['pointConversionRate'];
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
            <meta name="description" content="Manage NQK Bookstore policies">
            <link rel="stylesheet" href="/css/admin/policy/style.css">
            <title>Manage Policies</title>
            <?php storeToken(); ?>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class='w-100 h-100 d-flex'>
                        <div class='custom_container'>
                              <h1 class='mt-2 mx-auto'>Policies</h1>
                              <div class='mt-4 w-100 flex-grow-1 px-3'>
                                    <form class='d-flex flex-column' id='point_converion_form'>
                                          <div class='d-flex align-items-center'>
                                                <strong>Point Conversion</strong>
                                                <button type='button' onclick="resetConversion()" class='btn btn-sm btn-secondary ms-3'>Reset</button>
                                          </div>
                                          <div class='d-flex align-items-md-center mt-2 flex-md-row flex-column'>
                                                <p class='mb-0'>Convert</p>
                                                <input title='Point conversion rate' type='number' id='pointConversionRate' class='form-control mx-md-2 my-md-0 my-2' value="<?php echo $percentage; ?>">
                                                <p class='mb-0'>% of total order price into accumulated points</p>
                                                <div class='mt-md-0 mt-2'>
                                                      <button onclick="clearAllCustomValidity()" type='submit' class='btn btn-sm btn-light border border-1 border-dark rounded-pill ms-md-2'>Change</button>
                                                </div>
                                          </div>
                                    </form>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Changes Saved!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Your changes applied successfully!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            <script src="/javascript/admin/policy/script.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/input_validity.js"></script>
      </body>

      </html>

<?php } ?>