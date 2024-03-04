<?php
require_once __DIR__ . '../../../tool/php/session_check.php';
?>

<section id="header">
      <header class="w-100 h-100">
            <nav class="navbar navbar-expand-lg py-auto w-100">
                  <div class="container-fluid px-0">
                        <a class="navbar-brand d-flex align-items-center ms-2" href="/">
                              <!-- <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" id="logo_img"></img> -->
                              <img src="/image/logo.png" id="logo_img" title="NQK Bookstore demo logo"></img>
                              <p class="mb-0 ms-2">NQK Bookstore</p>
                        </a>
                        <button class="navbar-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                              <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse mt-2 mt-lg-0 me-lg-2 bg-white px-3" id="navbarSupportedContent">
                              <!-- Missing search functionality -->
                              <form class="d-flex align-items-center w-100 search_form_customer mt-lg-0 mt-2" role="search" id="search_form">
                                    <button aria-label="Search button" class="p-0 border-0 position-absolute bg-transparent mb-1 ms-2" type="submit">
                                          <svg fill="#000000" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.568">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M31.707 30.282l-9.717-9.776c1.811-2.169 2.902-4.96 2.902-8.007 0-6.904-5.596-12.5-12.5-12.5s-12.5 5.596-12.5 12.5 5.596 12.5 12.5 12.5c3.136 0 6.002-1.158 8.197-3.067l9.703 9.764c0.39 0.39 1.024 0.39 1.415 0s0.39-1.023 0-1.415zM12.393 23.016c-5.808 0-10.517-4.709-10.517-10.517s4.708-10.517 10.517-10.517c5.808 0 10.516 4.708 10.516 10.517s-4.709 10.517-10.517 10.517z"></path>
                                                </g>
                                          </svg>
                                    </button>
                                    <input id="search_book_customer" class="form-control me-2" type="search" placeholder="Search by name, author or ISBN number" aria-label="Search">
                              </form>
                              <ul class="navbar-nav ms-auto">
                                    <li class="nav-item mx-2">
                                          <a class="nav-link fs-5" href="/" id="home_nav">Home</a>
                                    </li>
                                    <li class="nav-item mx-2">
                                          <a class="nav-link fs-5" href="/book/" id="book_nav">Books</a>
                                    </li>
                                    <?php
                                    if (!check_session()) {
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5 text-nowrap" href="/authentication/" id="cart_nav">Cart</a>
                                                </li>';
                                          echo '<li class="nav-item ms-2">
                                                      <a class="nav-link fs-5 text-nowrap" href="/authentication/" id="signin_nav">Sign in</a>
                                                </li>';
                                    } else {
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5 text-nowrap" href="/cart/" id="cart_nav">Cart</a>
                                                </li>';
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5 text-nowrap\" href=\"/account/\" id=\"profile_nav\">Account</a>
                                          </li>";
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5 text-danger text-nowrap\" href=\"/ajax_service/authentication/logout\">Sign Out</a>
                                          </li>";
                                    }
                                    unset($_SESSION['update_book_id']);
                                    ?>
                              </ul>
                        </div>
                  </div>
            </nav>
      </header>
</section>