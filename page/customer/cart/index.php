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
                                          <div class='flex-column mb-4' id='fileSection'>
                                                <h4>E-books</h4>
                                                <div class="w-100 bg-white border rounded border-3 overflow-y-auto overflow-x-hidden item-container" id='fileList'></div>
                                          </div>

                                          <div class='flex-column' id='physicalSection'>
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
                                                <h4 class='mt-2'>Payment Detail</h4>
                                                <div>
                                                      <p>Payment Type</p>
                                                      <div class='d-flex'>
                                                            <input onchange="selectCardPayment(true)" type="radio" class="btn-check" id="card-payment" autocomplete="off" name='payment_method' checked>
                                                            <label class="btn btn-outline-secondary me-3" for="card-payment" data-bs-toggle="tooltip" data-bs-title="Credit or Debit Card">
                                                                  <svg width="32px" height="32px" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M196.6 311.6h660v129.2l15.8 76.1-15.8 63.7v184.5h-660z" fill="#2F2F33"></path>
                                                                              <path d="M860.7 444.5h-51.2v128.3h51.2v210.7c0 15.2-12.1 27.5-27.1 27.5h-637c-15 0-27.1-12.3-27.1-27.5V307c0-15.2 12.1-27.5 27.1-27.5h637c15 0 27.1 12.3 27.1 27.5v137.5z m-637-110V756h585.8V334.5H223.7z" fill="#2F2F33"></path>
                                                                              <path d="M794.3 304.3l-49.1 13.3-30.6-116.1-430.9 131.2-17.1-49.3 457.2-137.1c14.4-4.3 29.4 4 33.7 18.6 0.1 0.2 0.1 0.5 0.2 0.7l36.6 138.7zM887.8 389.467c15 0 27.1 12.3 27.1 27.5v183.2c0 15.2-12.1 27.5-27.1 27.5H707.1c-64.9 0-117.5-53.3-117.5-119.1s52.6-119.1 117.5-119.1h180.7z m-40.7 55h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7v-100.8c0-7.6-6.1-13.7-13.6-13.7z" fill="#2F2F33"></path>
                                                                              <path d="M860.7 444.5h-51.2v128.3h51.2v210.7c0 15.2-12.1 27.5-27.1 27.5h-637c-15 0-27.1-12.3-27.1-27.5V307c0-15.2 12.1-27.5 27.1-27.5h637c15 0 27.1 12.3 27.1 27.5v137.5z m-637-110V756h585.8V334.5H223.7z" fill="#2F2F33"></path>
                                                                              <path d="M794.3 304.3l-49.1 13.3-30.6-116.1-430.9 131.2-17.1-49.3 457.2-137.1c14.4-4.3 29.4 4 33.7 18.6 0.1 0.2 0.1 0.5 0.2 0.7l36.6 138.7zM887.8 389.467c15 0 27.1 12.3 27.1 27.5v183.2c0 15.2-12.1 27.5-27.1 27.5H707.1c-64.9 0-117.5-53.3-117.5-119.1s52.6-119.1 117.5-119.1h180.7z m-40.7 55h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7v-100.8c0-7.6-6.1-13.7-13.6-13.7z" fill="#2F2F33"></path>
                                                                              <path d="M860.7 444.5h-51.2v128.3h51.2v210.7c0 15.2-12.1 27.5-27.1 27.5h-637c-15 0-27.1-12.3-27.1-27.5V307c0-15.2 12.1-27.5 27.1-27.5h637c15 0 27.1 12.3 27.1 27.5v137.5z m-637-110V756h585.8V334.5H223.7z" fill="#2F2F33"></path>
                                                                              <path d="M794.3 304.3l-49.1 13.3-30.6-116.1-430.9 131.2-17.1-49.3 457.2-137.1c14.4-4.3 29.4 4 33.7 18.6 0.1 0.2 0.1 0.5 0.2 0.7l36.6 138.7zM887.8 389.467c15 0 27.1 12.3 27.1 27.5v183.2c0 15.2-12.1 27.5-27.1 27.5H707.1c-64.9 0-117.5-53.3-117.5-119.1s52.6-119.1 117.5-119.1h180.7z m-40.7 55h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7v-100.8c0-7.6-6.1-13.7-13.6-13.7z" fill="#2F2F33"></path>
                                                                              <path d="M457.5 280.3l255.2-79.5 24.2 86.8z" fill="#2F2F33"></path>
                                                                              <path d="M196.6 311.6h660v129.2l15.8 76.1-15.8 63.7v184.5h-660z" fill="#FFFFFF"></path>
                                                                              <path d="M860.7 444.5h-51.2v128.3h51.2v210.7c0 15.2-12.1 27.5-27.1 27.5h-637c-15 0-27.1-12.3-27.1-27.5V307c0-15.2 12.1-27.5 27.1-27.5h637c15 0 27.1 12.3 27.1 27.5v137.5z m-637-110V756h585.8V334.5H223.7z" fill="#2F2F33"></path>
                                                                              <path d="M794.3 304.3l-49.1 13.3-30.6-116.1-430.9 131.2-17.1-49.3 457.2-137.1c14.4-4.3 29.4 4 33.7 18.6 0.1 0.2 0.1 0.5 0.2 0.7l36.6 138.7zM887.8 389.467c15 0 27.1 12.3 27.1 27.5v183.2c0 15.2-12.1 27.5-27.1 27.5H707.1c-64.9 0-117.5-53.3-117.5-119.1s52.6-119.1 117.5-119.1h180.7z m-40.7 55h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7v-100.8c0-7.6-6.1-13.7-13.6-13.7z" fill="#2F2F33"></path>
                                                                              <path d="M860.7 444.5h-51.2v128.3h51.2v210.7c0 15.2-12.1 27.5-27.1 27.5h-637c-15 0-27.1-12.3-27.1-27.5V307c0-15.2 12.1-27.5 27.1-27.5h637c15 0 27.1 12.3 27.1 27.5v137.5z m-637-110V756h585.8V334.5H223.7z" fill="#2F2F33"></path>
                                                                              <path d="M794.3 304.3l-49.1 13.3-30.6-116.1-430.9 131.2-17.1-49.3 457.2-137.1c14.4-4.3 29.4 4 33.7 18.6 0.1 0.2 0.1 0.5 0.2 0.7l36.6 138.7zM887.8 389.467c15 0 27.1 12.3 27.1 27.5v183.2c0 15.2-12.1 27.5-27.1 27.5H707.1c-64.9 0-117.5-53.3-117.5-119.1s52.6-119.1 117.5-119.1h180.7z m-40.7 55h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7v-100.8c0-7.6-6.1-13.7-13.6-13.7z" fill="#2F2F33"></path>
                                                                              <path d="M457.5 280.3l255.2-79.5 24.2 86.8z" fill="#8CAAFF"></path>
                                                                              <path d="M847.4 445h-140c-34.9 0-63.2 28.7-63.2 64.1s28.3 64.1 63.2 64.1h140c7.5 0 13.6-6.2 13.6-13.7V458.7c-0.1-7.6-6.1-13.7-13.6-13.7z" fill="#FFFFFF"></path>
                                                                              <path d="M686.4 506.5a27.1 27.5 0 1 0 54.2 0 27.1 27.5 0 1 0-54.2 0Z" fill="#2F2F33"></path>
                                                                        </g>
                                                                  </svg>
                                                            </label>
                                                            <input onchange="selectCardPayment(false)" type="radio" class="btn-check" id="paypal-payment" autocomplete="off" name='payment_method'>
                                                            <label class="btn btn-outline-secondary me-3" for="paypal-payment" data-bs-toggle="tooltip" data-bs-title="PayPal">
                                                                  <svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32px" height="32px" viewBox="0 0 25.793 25.793" xml:space="preserve">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <g>
                                                                                    <path id="PayPal_3_" d="M16.64,0H5.345L0.341,22.971h6.627l1.623-7.611h4.734c4.527,0,8.316-2.793,9.332-7.54 C23.806,2.443,19.951,0,16.64,0z M11.699,10.959h-2.16l1.42-6.282h3.246c1.112,0,1.957,0.662,2.233,1.637 c-0.144-0.024-0.274-0.067-0.431-0.067h-3.246L11.699,10.959z M16.437,7.818c-0.396,1.705-2.018,3.064-3.666,3.129l0.776-3.444 h2.94C16.472,7.607,16.464,7.708,16.437,7.818z M24.463,9.387c0.38-1.779,0.204-3.23-0.312-4.387 c1.072,1.28,1.629,3.141,1.096,5.643c-1.018,4.748-4.805,7.539-9.332,7.539h-4.734l-1.623,7.611H2.93l0.274-1.256h5.567 l1.623-7.609h4.735C19.66,16.928,23.448,14.135,24.463,9.387z"></path>
                                                                              </g>
                                                                        </g>
                                                                  </svg>
                                                            </label>
                                                            <input onchange="selectCardPayment(false)" type="radio" class="btn-check" id="cod-payment" autocomplete="off" name='payment_method'>
                                                            <label class="btn btn-outline-secondary" for="cod-payment" data-bs-toggle="tooltip" data-bs-title="Cash on Delivery">
                                                                  <svg fill="#ffffff" width="32px" height="32px" viewBox="0 0 32 32" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#ffffff">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <g transform="matrix(1,0,0,1,-240,-384)">
                                                                                    <g transform="matrix(1.2,0,0,1,66.4,93)">
                                                                                          <path d="M168,300.472C168,300.162 167.94,299.855 167.824,299.578C167.441,298.659 166.521,296.452 165.961,295.106C165.678,294.428 165.101,294 164.47,294C161.728,294 154.272,294 151.53,294C150.899,294 150.322,294.428 150.039,295.106C149.479,296.452 148.559,298.659 148.176,299.578C148.06,299.855 148,300.162 148,300.472C148,302.843 148,313.511 148,318C148,318.53 148.176,319.039 148.488,319.414C148.801,319.789 149.225,320 149.667,320C153.433,320 162.567,320 166.333,320C166.775,320 167.199,319.789 167.512,319.414C167.824,319.039 168,318.53 168,318C168,313.511 168,302.843 168,300.472Z" style="fill:#ffffff;"></path>
                                                                                    </g>
                                                                                    <path d="M263.764,386L248.236,386C247.1,386 246.061,386.642 245.553,387.658L243.317,392.13C243.108,392.547 243,393.006 243,393.472C243,395.843 243,406.511 243,411C243,411.796 243.316,412.559 243.879,413.121C244.441,413.684 245.204,414 246,414C250.52,414 261.48,414 266,414C266.796,414 267.559,413.684 268.121,413.121C268.684,412.559 269,411.796 269,411L269,393.472C269,393.006 268.892,392.547 268.683,392.131L266.447,387.658C265.939,386.642 264.9,386 263.764,386ZM267,394L260,394L260,397.955C260,398.719 259.565,399.416 258.879,399.752C258.193,400.088 257.375,400.003 256.772,399.534L256,398.934L255.228,399.534C254.625,400.003 253.807,400.088 253.121,399.752C252.435,399.416 252,398.719 252,397.955L252,394L245,394L245,411C245,411.265 245.105,411.52 245.293,411.707C245.48,411.895 245.735,412 246,412L266,412C266.265,412 266.52,411.895 266.707,411.707C266.895,411.52 267,411.265 267,411C267,411 267,394 267,394ZM249.886,407.998C248.283,407.938 247,406.618 247,405C247,403.344 248.344,402 250,402L251,402C251.552,402 252,402.448 252,403C252,403.552 251.552,404 251,404C251,404 250,404 250,404C249.448,404 249,404.448 249,405C249,405.552 249.448,406 250,406L250.888,406C251.44,406 251.888,406.448 251.888,407C251.888,407.535 251.468,407.972 250.94,407.999L249.888,408L249.886,407.998ZM260,407C260,407.552 260.448,408 261,408L262,408C263.656,408 265,406.656 265,405C265,403.344 263.656,402 262,402L261,402C260.448,402 260,402.448 260,403L260,407ZM256,402C254.344,402 253,403.344 253,405C253,406.656 254.344,408 256,408C257.656,408 259,406.656 259,405C259,403.344 257.656,402 256,402ZM262,406C262.552,406 263,405.552 263,405C263,404.448 262.552,404 262,404L262,406ZM256,404C256.552,404 257,404.448 257,405C257,405.552 256.552,406 256,406C255.448,406 255,405.552 255,405C255,404.448 255.448,404 256,404ZM258,394L258,397.955C257.29,397.403 256.614,396.877 256.614,396.877C256.253,396.596 255.747,396.596 255.386,396.877L254,397.955L254,394L258,394ZM252.82,388L252.153,392L245.618,392L247.342,388.553C247.511,388.214 247.857,388 248.236,388L252.82,388ZM254.18,392L254.847,388L257.153,388L257.82,392L254.18,392ZM259.18,388L263.764,388C264.143,388 264.489,388.214 264.658,388.553L266.382,392L259.847,392L259.18,388Z" style="fill:#000000;"></path>
                                                                              </g>
                                                                        </g>
                                                                  </svg>
                                                            </label>
                                                      </div>
                                                      <div class='mt-3' id='card_input'>
                                                            <p>Card Type</p>
                                                            <div class='d-flex'>
                                                                  <input type="radio" class="btn-check" id="visa-payment" autocomplete="off" name='card_method'>
                                                                  <label class="btn btn-outline-secondary me-3" for="visa-payment" data-bs-toggle="tooltip" data-bs-title="Visa">
                                                                        <svg fill="#000000" width="32px" height="32px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                                              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                              <g id="SVGRepo_iconCarrier">
                                                                                    <title>visa</title>
                                                                                    <path d="M15.854 11.329l-2.003 9.367h-2.424l2.006-9.367zM26.051 17.377l1.275-3.518 0.735 3.518zM28.754 20.696h2.242l-1.956-9.367h-2.069c-0.003-0-0.007-0-0.010-0-0.459 0-0.853 0.281-1.019 0.68l-0.003 0.007-3.635 8.68h2.544l0.506-1.4h3.109zM22.429 17.638c0.010-2.473-3.419-2.609-3.395-3.714 0.008-0.336 0.327-0.694 1.027-0.785 0.13-0.013 0.28-0.021 0.432-0.021 0.711 0 1.385 0.162 1.985 0.452l-0.027-0.012 0.425-1.987c-0.673-0.261-1.452-0.413-2.266-0.416h-0.001c-2.396 0-4.081 1.275-4.096 3.098-0.015 1.348 1.203 2.099 2.122 2.549 0.945 0.459 1.262 0.754 1.257 1.163-0.006 0.63-0.752 0.906-1.45 0.917-0.032 0.001-0.071 0.001-0.109 0.001-0.871 0-1.691-0.219-2.407-0.606l0.027 0.013-0.439 2.052c0.786 0.315 1.697 0.497 2.651 0.497 0.015 0 0.030-0 0.045-0h-0.002c2.546 0 4.211-1.257 4.22-3.204zM12.391 11.329l-3.926 9.367h-2.562l-1.932-7.477c-0.037-0.364-0.26-0.668-0.57-0.82l-0.006-0.003c-0.688-0.338-1.488-0.613-2.325-0.786l-0.066-0.011 0.058-0.271h4.124c0 0 0.001 0 0.001 0 0.562 0 1.028 0.411 1.115 0.948l0.001 0.006 1.021 5.421 2.522-6.376z"></path>
                                                                              </g>
                                                                        </svg>
                                                                  </label>
                                                                  <input type="radio" class="btn-check" id="mastercard-payment" autocomplete="off" name='card_method'>
                                                                  <label class="btn btn-outline-secondary me-3" for="mastercard-payment" data-bs-toggle="tooltip" data-bs-title="Mastercard">
                                                                        <svg width="32px" height="32px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                              <g id="SVGRepo_iconCarrier">
                                                                                    <g>
                                                                                          <path fill="none" d="M0 0h24v24H0z"></path>
                                                                                          <path fill-rule="nonzero" d="M12 18.294a7.3 7.3 0 1 1 0-12.588 7.3 7.3 0 1 1 0 12.588zm1.702-1.384a5.3 5.3 0 1 0 0-9.82A7.273 7.273 0 0 1 15.6 12c0 1.89-.719 3.614-1.898 4.91zm-3.404-9.82a5.3 5.3 0 1 0 0 9.82A7.273 7.273 0 0 1 8.4 12c0-1.89.719-3.614 1.898-4.91zM12 8.205A5.284 5.284 0 0 0 10.4 12c0 1.488.613 2.832 1.6 3.795A5.284 5.284 0 0 0 13.6 12 5.284 5.284 0 0 0 12 8.205z"></path>
                                                                                    </g>
                                                                              </g>
                                                                        </svg>
                                                                  </label>
                                                                  <input type="radio" class="btn-check" id="jcb-payment" autocomplete="off" name='card_method'>
                                                                  <label class="btn btn-outline-secondary" for="jcb-payment" data-bs-toggle="tooltip" data-bs-title="JCB">
                                                                        <svg fill="#000000" width="32px" height="32px" viewBox="0 0 24.00 24.00" role="img" xmlns="http://www.w3.org/2000/svg" transform="matrix(1, 0, 0, 1, 0, 0)" stroke="#000000" stroke-width="0.00024000000000000003">
                                                                              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.048"></g>
                                                                              <g id="SVGRepo_iconCarrier">
                                                                                    <path d="M13.05 9.864c.972.074 1.726.367 2.354.685v-1.31s-1.257-.317-2.44-.368C8.838 8.686 7.669 10.305 7.669 12s1.17 3.314 5.295 3.13c1.183-.054 2.44-.37 2.44-.37v-1.309c-.619.308-1.382.611-2.354.683-1.68.128-2.69-.69-2.69-2.134 0-1.445 1.01-2.261 2.69-2.135m7.685 4.122a1.48 1.48 0 0 1-.215.02h-1.8v-1.631h1.8c.057 0 .164.01.215.02a.806.806 0 0 1 .632.795.804.804 0 0 1-.632.796zM18.72 9.95h1.632c.059 0 .145.007.177.013a.736.736 0 0 1 .626.74.735.735 0 0 1-.626.739 1.571 1.571 0 0 1-.178.013h-1.63V9.951zm3.499 1.985V11.9c.913-.133 1.415-.726 1.415-1.42 0-.883-.734-1.392-1.73-1.442-.077-.003-.202-.01-.304-.01h-5.332v5.946h5.755c1.13 0 1.977-.604 1.977-1.547 0-.87-.772-1.422-1.781-1.491zm-17.864.68c0 .878-.591 1.53-1.666 1.53-.917 0-1.817-.272-2.689-.694v1.309s1.402.383 3.191.383c2.971 0 3.837-1.125 3.837-2.529V9.027H4.354v3.587z"></path>
                                                                              </g>
                                                                        </svg>
                                                                  </label>
                                                            </div>
                                                            <div class="form-floating mt-3">
                                                                  <input type="text" class="form-control border-2" id="card-holder-name" placeholder="Cardholder's Name">
                                                                  <label for="card-holder-name">Cardholder's Name</label>
                                                            </div>
                                                            <div class="form-floating mt-3">
                                                                  <input type="text" class="form-control border-2" id="card-number" placeholder="1234 5678 9012 3456" maxlength="16">
                                                                  <label for="card-number">Card Number</label>
                                                            </div>
                                                            <div class='d-flex mt-3 flex-column flex-md-row'>
                                                                  <div class="form-floating">
                                                                        <input type="month" class="form-control border-2" id="card-expiration" placeholder="MM/YYYY">
                                                                        <label for="card-expiration">Expiration</label>
                                                                  </div>
                                                                  <div class="form-floating ms-md-3 mt-3 mt-md-0">
                                                                        <input type="password" class="form-control border-2" id="card-cvv" placeholder="" maxlength="3" autocomplete="off">
                                                                        <label for="card-cvv">CVV</label>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <hr>
                                                <h4 class='mt-2'>Price Detail</h4>
                                                <p>Total Before Discount Coupons:&nbsp;<span class='fw-medium' id='totalPriceBeforeCoupon'>0</span></p>
                                                <p>Total After Discount Coupons:&nbsp;<span class='fw-medium' id='totalPriceAfterCoupon'>0</span></p>
                                                <p>Loyalty Discount:&nbsp;<span class='fw-medium' id='loyalDiscount'>0</span></p>
                                                <p>Referrer Discount:&nbsp;<span class='fw-medium' id='refDiscount'>0</span></p>
                                                <p>Total Discount:&nbsp;<span class='fw-medium' id='totalDiscount'>0</span></p>
                                                <h4>Final Price:&nbsp;<span id='finalPrice'>0</span></h4>
                                                <button id='purchaseBtn' onclick="placeOrder()" type="button" class="btn btn-primary text-white fw-medium mb-2 w-100 mt-3"><i class="bi bi-cart4"></i> Place Order</button>
                                          </div>
                                    </div>
                              </div>
                        </form>
                  </div>
                  <div class="modal fade" id='confirmModal' tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h5 class="modal-title">Confirm Order</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                          <p>Are you sure you want to purchase this order?</p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="payOrder()">Confirm</button>
                                    </div>
                              </div>
                        </div>
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
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="$('#card-payment').prop('checked',true); selectCardPayment(true);">Confirm</button>
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
      </body>

      </html>

<?php } ?>