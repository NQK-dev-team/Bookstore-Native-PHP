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

                        <h5 id='fileCopyTitle'>File Copies</h5>
                        <div class="w-100 bg-white border rounded" id='fileList'>
                              <div class='row'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="/image/default_male.jpeg" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>Book name</strong>
                                                <strong class='text-md-start text-center'>1st edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$100</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'></div>
                                    <div class='col-lg-2 col-12'></div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto"></i>
                                    </div>
                              </div>
                              <hr class='my-2'>
                        </div>

                        <h5 class='mt-5' id='physicalCopyTitle'>Physical Copies</h5>
                        <p>
                              <label class='fw-bold form-label' for="physicalDestination">Delivery Address:&nbsp;</label>
                              <input id='physicalDestination' class='form-control'></input>
                        </p>
                        <div class="w-100 bg-white border rounded" id='physicalList'>
                              <div class='row'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="/image/default_male.jpeg" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>Book name</strong>
                                                <strong class='text-md-start text-center'>1st edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$100</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'>
                                          <div class="btn-group my-lg-auto mx-lg-0 mx-auto mt-2" role="group">
                                                <input type="button" class="btn-check" name="btnradio" autocomplete="off">
                                                <label class="btn btn-outline-danger" for="btnradio">-</label>

                                                <input type="number" class="fw-bold ammount_input ps-2" id="book_ammount" autocomplete="off" value="1000">

                                                <input type="button" class="btn-check" name="btnradio" autocomplete="off">
                                                <label class="btn btn-outline-success" for="btnradio">+</label>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12'>
                                          <div class='w-100 h-100 d-flex justify-content-lg-start justify-content-center mt-lg-0 my-2'>
                                                <strong class='my-auto'>In stock:&nbsp;</strong>
                                                <strong class='my-auto'>100</strong>
                                          </div>
                                    </div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto mt-2"></i>
                                    </div>
                              </div>
                              <hr class='my-2'>
                        </div>
                        <div class='my-3 w-100'>
                              <h4>Price Detail</h4>
                              <div class='d-flex'>
                                    <p>Total Before Discount:&nbsp;</p>
                                    <p class='mb-0' id='totalPriceBefore'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Total After Discount:&nbsp;</p>
                                    <p class='mb-0' id='totalPriceAfter'>0</p>
                              </div>
                              <div class='d-flex'>
                                    <p>Discount:&nbsp;</p>
                                    <p class='mb-0' id='totalDiscount'>0</p>
                              </div>
                        </div>
                        <button id='purchaseBtn' type="submit" class="btn btn-primary mb-3 mt-auto" data-bs-toggle="modal"><i class="bi bi-cart4"></i> Place Order</button>
                  </form>
                  <div class="modal" id='paymentModal' tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Choose Payment Method</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <div class='w-100 h-100 d-flex flex-lg-row flex-column'>
                                                <input type="radio" class="btn-check" id="visaCheck" autocomplete="off" name='paymentMethod'>
                                                <label class="btn btn-outline-secondary p-1 mx-auto pointer my-lg-0 my-2 border rounded" for="visaCheck">
                                                      <img src='/image/visa.jpeg' class='payment'>
                                                </label>

                                                <input type="radio" class="btn-check" id="mcCheck" autocomplete="off" name='paymentMethod'>
                                                <label class="btn btn-outline-secondary p-1 mx-auto pointer my-lg-0 my-2 border rounded" for="mcCheck">
                                                      <img src='/image/mastercard.png' class='payment'>
                                                </label>
                                          </div>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
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
                  <div class="modal" id="paymentSuccess" tabindex="-1">
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
                  <div class="modal" id="delModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Delete from cart</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <h2>Are you sure you want to delete this book from your cart?</h2>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          <!-- Add data handling here -->
                                          <button type="button" class="btn btn-primary">Confirm</button>
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