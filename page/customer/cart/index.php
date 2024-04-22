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
                  <div class='w-100 h-100 d-flex'>
                        <form class='bg-white border border-3 rounded m-auto p-3 d-flex flex-column' id='cartForm'>
                              <h1 class='mt-2 fs-2'>Shopping Cart</h1>
                              <hr>
                              <div class='row flex-grow-1 overflow-hidden'>
                                    <div class='col-lg-8 col-12 d-flex flex-column'>
                                          <div class='flex-column mb-4 d-flex' id='fileSection'>
                                                <h4>E-books</h4>
                                                <div class="w-100 bg-white border rounded border-3 overflow-y-auto overflow-x-hidden item-container" id='fileList'></div>
                                          </div>

                                          <div class='flex-column d-flex' id='physicalSection'>
                                                <h4>Hardcovers</h4>
                                                <div class='mb-2'>
                                                      <label class='fw-bold form-label' for="physicalDestination">Delivery Address:&nbsp;</label>
                                                      <input id='physicalDestination' class='form-control border-3'></input>
                                                </div>
                                                <div class="w-100 bg-white border rounded border-3 overflow-y-auto overflow-x-hidden mb-lg-4 item-container" id='physicalList'></div>
                                          </div>
                                    </div>
                                    <div class='col-lg-4 col-12 mt-3 mt-lg-0'>
                                          <div class='border border-3 rounded py-2 px-3 mb-5'>
                                                <h4 class='mt-2'>Price Detail</h4>
                                                <p>Total Before Discount Coupons:&nbsp;<span class='fw-medium' id='totalPriceBeforeCoupon'>0</span></p>
                                                <p>Total After Discount Coupons:&nbsp;<span class='fw-medium' id='totalPriceAfterCoupon'>0</span></p>
                                                <p>Loyalty Discount:&nbsp;<span class='fw-medium' id='loyalDiscount'>0</span></p>
                                                <p>Referrer Discount:&nbsp;<span class='fw-medium' id='refDiscount'>0</span></p>
                                                <p>Total Discount:&nbsp;<span class='fw-medium' id='totalDiscount'>0</span></p>
                                                <h4>Final Price:&nbsp;<span id='finalPrice'>0</span></h4>
                                                <hr>
                                                <button onclick="if(placeOrder()) $('#cartForm').submit();" type="button" class="btn btn-primary customizeButton text-white mb-3 w-100 mt-3 fs-4">Cash On Delivery</button>
                                                <div id='paypal_button_container'></div>
                                          </div>
                                    </div>
                              </div>
                        </form>
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
            <script src="/tool/js/tool_tip.js"></script>
            <script src="https://www.paypal.com/sdk/js?client-id=AeJzcuBeYdWuSATJhEg4Y6VELgzJlgrjby07Upgt5V88gwHWeFeeBdJi121zROOe0MaOIvQ6ACBvG0Km&currency=USD"></script>
            <script>
                  paypal.Buttons({
                        style: {
                              layout: 'vertical',
                              color: 'gold',
                              shape: 'rect',
                              label: 'pay',
                              height: 55,
                              borderRadius: 10,
                              disableMaxWidth: true
                        },
                        createOrder: function(data, actions) {
                              if (placeOrder())
                                    return actions.order.create({
                                          purchase_units: [{
                                                amount: {
                                                      value: $('#finalPrice').text().substring(1)
                                                }
                                          }]
                                    });
                              return actions.order.create(null);
                        },
                        onApprove(data, actions) {
                              $("#cartForm").submit();
                        }
                  }).render('#paypal_button_container')
            </script>
      </body>

      </html>

<?php } ?>