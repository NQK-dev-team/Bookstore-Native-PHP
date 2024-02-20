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
      require_once __DIR__ . '/../../../tool/php/formatter.php';

      unset($_SESSION['update_book_id']);

      require_once __DIR__ . '/../../../config/db_connection.php';

      try {
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }

            $stmt = $conn->prepare("SELECT COUNT(*) as result FROM customer where status=true");
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
                  exit;
            }
            $totalEntries = $stmt->get_result()->fetch_assoc()['result'];
            $stmt->close();

            $elem = '';

            $stmt = $conn->prepare('select name,email,phone,dob,gender,point,cardNumber,address,appUser.id from appUser join customer on customer.id=appUser.id where status=true order by point desc,name,email,customer.id limit 10');
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
                  exit;
            }
            $result = $stmt->get_result();
            $idx = 0;
            while ($row = $result->fetch_assoc()) {
                  $idx++;
                  $elem .= '<tr>';
                  $elem .= "<td class='align-middle'>{$idx}</td>";
                  $elem .= "<td class='align-middle col-2'>{$row['name']}</td>";
                  $row['email'] = $row['email'] ? $row['email'] : 'N/A';
                  $elem .= "<td class='align-middle col-2'>{$row['email']}</td>";
                  $row['phone'] = $row['phone'] ? $row['phone'] : 'N/A';
                  $elem .= "<td class='align-middle col-1'>{$row['phone']}</td>";
                  $row['dob'] = MDYDateFormat($row['dob']);
                  $elem .= "<td class='align-middle col-1'>{$row['dob']}</td>";
                  $row['address'] = $row['address'] ? $row['address'] : 'N/A';
                  $elem .= "<td class='align-middle col-2'>{$row['address']}</td>";
                  $row['gender'] = $row['gender'] === 'M' ? 'Male' : ($row['gender'] === 'F' ? 'Female' : 'Other');
                  $elem .= "<td class='align-middle'>{$row['gender']}</td>";
                  $row['point'] = round($row['point'], 2);
                  $elem .= "<td class='align-middle'>{$row['point']}</td>";
                  $row['cardNumber'] = $row['cardNumber'] ? $row['cardNumber'] : 'N/A';
                  $elem .= "<td class='align-middle col-1'>{$row['cardNumber']}</td>";
                  $elem .= "<td class='align-middle col-1'>
                        <div class='d-flex flex-lg-row flex-column'>
                              <a title='Visit customer detailed information page' class='btn btn-sm btn-info text-white' href='./detail?id={$row['id']}' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\"><i class=\"bi bi-info-circle\"></i></a>
                              <button title='deactivate customer' onclick='openDeactivateModal(\"{$row['id']}\")' class='btn btn-sm btn-danger text-white ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Deactivate\"><i class=\"bi bi-power\"></i></button>
                              <button title='delete customer' onclick='openDeleteModal(\"{$row['id']}\")' class='btn btn-sm btn-danger text-white ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\"><i class=\"bi bi-trash3-fill\"></i></button>
                        </div>
                  </td>";
                  $elem .= "</tr>";
            }
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
            <meta name="description" content="Manage customers of NQK Bookstore">
            <title>Manage Customers</title>
            <link rel="stylesheet" href="/css/admin/customer/customer_list.css">
            <?php storeToken(); ?>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div class="container-fluid h-100 d-flex flex-column">
                        <h1 class='fs-2 mx-auto mt-3'>Customer List</h1>
                        <div class='mt-2 d-flex flex-column flex-lg-row align-items-center'>
                              <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1" role="search" id="search_form">
                                    <button title='search customer' class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                          <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                </g>
                                          </svg>
                                    </button>

                                    <input id="search_customer" class="form-control me-2" type="search" placeholder="Search by name, email, phone number or card number" aria-label="Search">
                              </form>
                        </div>
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
                              <div class="mt-2">
                                    <div class="form-check form-switch">
                                          <label class="form-check-label text-success" for="flexSwitchCheckDefault" id="switch_label">Choose active customers</label>
                                          <input class="form-check-input pointer" type="checkbox" role="switch" id="flexSwitchCheckDefault" checked onchange="updateSwitchLabel()">
                                    </div>
                              </div>
                        </div>
                        <div class="w-100 overflow-x-auto">
                              <table class="table table-hover border border-2 table-bordered mt-4 w-100">
                                    <thead id="table_header">
                                          <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Phone Number</th>
                                                <th scope="col">Date of Birth</th>
                                                <th scope="col">Address</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col" class='text-nowrap'>Accumulated Points</th>
                                                <th scope="col">Card Number</th>
                                                <th scope="col">Action</th>
                                          </tr>
                                    </thead>
                                    <tbody id="table_body">
                                          <?php
                                          echo $elem;
                                          ?>
                                    </tbody>
                              </table>
                        </div>
                        <div class="w-100 d-flex flex-sm-row flex-column justify-content-sm-between mb-4 mt-2 align-items-center">
                              <div class="d-flex">
                                    <p>Show&nbsp;</p>
                                    <p id="start_entry">
                                          <?php
                                          if ($totalEntries === 0) echo '0';
                                          else echo '1'; ?>
                                    </p>
                                    <p>&nbsp;to&nbsp;</p>
                                    <p id="end_entry">
                                          <?php
                                          if ($totalEntries < 10) echo $totalEntries;
                                          else echo '10'; ?>
                                    </p>
                                    <p>&nbsp;of&nbsp;</p>
                                    <p id="total_entries"><?php echo $totalEntries; ?></p>
                                    <p>&nbsp;entries</p>
                              </div>
                              <div class="group_button">
                                    <div class="btn-group d-flex" role="group">
                                          <button type="button" class="btn btn-outline-info" id="prev_button" onClick="changeList(false)" disabled>Previous</button>
                                          <button type="button" class="btn btn-info text-white" disabled id="list_offset">1</button>
                                          <button type="button" class="btn btn-outline-info" id="next_button" onClick="changeList(true)" <?php if ($totalEntries !== "N/A" && 10 >= $totalEntries) echo 'disabled'; ?>>Next</button>
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
                  <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Confirm Deactivation</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>Are you sure you want to deactivate this customer?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="deactivateCustomer()">Confirm</button>
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
                                          <p>Are you sure you want to activate this customer?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="activateCustomer()">Confirm</button>
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
                                          <p>Are you sure you want to delete this customer?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="deleteCustomer()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deleteNotifyModal1" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Delete Request Sent!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>This customer information will be deleted after 14 days!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deleteNotifyModal2" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Customer Deleted!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>This customer information has been deleted!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deleteCancelNotifyModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Deletion Process Cancelled!</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p>This customer deletion process has been cancelled!</p>
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
            <script src="/javascript/admin/menu_after_load.js"></script>
            <script src="/javascript/admin/customer/customer_list.js"></script>
            <script src="/javascript/admin/customer/toggle_status.js"></script>
            <script src="/javascript/admin/customer/delete_customer.js"></script>
            <script src="/tool/js/tool_tip.js"></script>
            <script src="/tool/js/encoder.js"></script>
      </body>

      </html>

<?php } ?>