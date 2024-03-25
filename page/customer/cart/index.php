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
} else {
      require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <link rel="stylesheet" href="/css/customer/cart/cart.css">

            <meta name="author" content="Quang Nguyen, Nghia Duong">
            <meta name="description" content="Customer's cart">
            <title>My Cart</title>
            <?php storeToken(); ?>

      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <form class='w-100 h-100 d-flex flex-column container-fluid' id='cartForm'>
                        <h1 class='mt-2 mx-auto fs-2'>My Shopping Cart</h1>

                        <h5>File Copies</h5>
                        <div class="w-100 bg-white border rounded mb-5" id='fileList'></div>

                        <h5>Physical Copies</h5>
                        <div class='mb-2'>
                              <label class='fw-bold form-label' for="physicalDestination">Delivery Address:&nbsp;</label>
                              <input id='physicalDestination' class='form-control'></input>
                        </div>
                        <div class="w-100 bg-white border rounded" id='physicalList'></div>
                        <div class='my-3 w-100 mt-auto'>
                              <h4 class='mt-3'>Price Detail</h4>
                              <div class='d-flex'>
                                    <p>Total Before Discount Coupons:&nbsp;</p>
                                    <p class='mb-0' id='totalPriceBeforeCoupon'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Total After Discount Coupons:&nbsp;</p>
                                    <p class='mb-0' id='totalPriceAfterCoupon'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Loyalty Discount:&nbsp;</p>
                                    <p class='mb-0' id='loyalDiscount'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Referrer Discount:&nbsp;</p>
                                    <p class='mb-0' id='refDiscount'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Final Price:&nbsp;</p>
                                    <p class='mb-0' id='finalPrice'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Total Discount:&nbsp;</p>
                                    <p class='mb-0' id='totalDiscount'>0</p>
                              </div>
                              <button id='purchaseBtn' type="submit" class="btn btn-primary mb-3 w-100"><i class="bi bi-cart4"></i> Place Order</button>
                        </div>
                  </form>
                  <div class="modal fade" id='paymentModal' tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Choose Payment Method</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <div class='w-100 h-100 d-flex flex-lg-row flex-column'>
                                                <input type="radio" class="btn-check" id="visaCheck" autocomplete="off" name='paymentMethod' value='1'>
                                                <label class="btn btn-outline-secondary p-1 mx-auto pointer my-lg-0 my-2 border rounded" for="visaCheck">
                                                      <img src='/image/visa.jpeg' class='payment'>
                                                </label>

                                                <input type="radio" class="btn-check" id="mcCheck" autocomplete="off" name='paymentMethod' value='2'>
                                                <label class="btn btn-outline-secondary p-1 mx-auto pointer my-lg-0 my-2 border rounded" for="mcCheck">
                                                      <img src='/image/mastercard.png' class='payment'>
                                                </label>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" onclick="payOrder()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id='noPaymentModal' tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Payment Method Not Chosen</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <p>Please choose a payment method!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class=" modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
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
                  <div class="modal fade" id="paymentSuccess" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Order Purchased</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <p>You order has been purchased successfully!</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="modal fade" id="deleteModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Delete From Cart</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <p>Are you sure you want to delete this book from your cart?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          <button type="button" class="btn btn-danger" onclick="removeBook()">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/javascript/customer/cart/script.js"></script>
            <script src="/tool/js/encoder.js"></script>
            <script src="/tool/js/input_validity.js"></script>
      </body>

      </html>

<?php } ?>