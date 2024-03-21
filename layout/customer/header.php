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