<?php
require_once __DIR__ . '../../../tool/php/session_check.php';
?>

<section id="header">
      <header class="w-100 h-100">
            <nav class="navbar navbar-expand-lg py-auto w-100">
                  <div class="container-fluid px-0">
                        <a class="navbar-brand d-flex align-items-center ms-2" href="/admin/">
                              <img src="/image/logo.png" id="logo_img" title="NQK Bookstore logo"></img>
                              <p class="mb-0 ms-2">NQK Bookstore</p>
                        </a>
                        <button class="navbar-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                              <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse mt-2 mt-lg-0 me-lg-2 bg-white px-3" id="navbarSupportedContent">
                              <ul class="navbar-nav ms-auto">
                                    <li class="nav-item mx-2">
                                          <a class="nav-link fs-5 d-inline-block" href="/admin/" id="home_nav">Home</a>
                                    </li>
                                    <li class="nav-item dropdown mx-2">
                                          <p class="nav-link m-0 fs-5 d-inline-block" id="manage_dropdown_0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Manage
                                                <svg width="16px" height="16px" fill="#000000" stroke="#000000" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve" stroke-width="5">
                                                      <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                      <g id="SVGRepo_iconCarrier">
                                                            <g>
                                                                  <path d="M78.466,35.559L50.15,63.633L22.078,35.317c-0.777-0.785-2.044-0.789-2.828-0.012s-0.789,2.044-0.012,2.827L48.432,67.58 c0.365,0.368,0.835,0.563,1.312,0.589c0.139,0.008,0.278-0.001,0.415-0.021c0.054,0.008,0.106,0.021,0.16,0.022 c0.544,0.029,1.099-0.162,1.515-0.576l29.447-29.196c0.785-0.777,0.79-2.043,0.012-2.828S79.249,34.781,78.466,35.559z"></path>
                                                            </g>
                                                      </g>
                                                </svg>
                                          </p>
                                          <ul class="dropdown-menu">
                                                <?php
                                                if (!check_session())
                                                      echo '<li><a id="manage_dropdown_1" class="dropdown-item" href="/admin/authentication/">Book</a></li>
                                                <li><a id="manage_dropdown_2" class="dropdown-item" href="/admin/authentication/">Category</a></li>
                                                <li><a id="manage_dropdown_3" class="dropdown-item" href="/admin/authentication/">Customer</a></li>
                                                <li><a id="manage_dropdown_4" class="dropdown-item" href="/admin/authentication/">Coupon</a></li>';
                                                else if (check_session() && $_SESSION['type'] === 'admin')
                                                      echo '<li><a id="manage_dropdown_1" class="dropdown-item" href="/admin/book/">Book</a></li>
                                                <li><a id="manage_dropdown_2" class="dropdown-item" href="/admin/category/">Category</a></li>
                                                <li><a id="manage_dropdown_3" class="dropdown-item" href="/admin/customer/">Customer</a></li>
                                                <li><a id="manage_dropdown_4" class="dropdown-item" href="/admin/coupon/">Coupon</a></li>';
                                                ?>
                                          </ul>
                                    </li>
                                    <li class="nav-item mx-2">
                                          <?php
                                          if (!check_session())
                                                echo '<a id="statistic_nav" class="nav-link d-inline-block fs-5" href="/admin/authentication/">Statistic</a>';
                                          else if (check_session() && $_SESSION['type'] === 'admin')
                                                echo '<a id="statistic_nav" class="nav-link d-inline-block fs-5" href="/admin/statistic/">Statistic</a>';
                                          ?>
                                    </li>
                                    <?php
                                    if (!check_session()) {
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link d-inline-block fs-5\" href=\"/admin/authentication/\" id=\"profile_nav\">Account</a>
                                          </li>";
                                          echo '<li class="nav-item ms-2">
                                                      <a class="nav-link d-inline-block fs-5" href="/admin/authentication/" id="signin_nav">Sign in</a>
                                                </li>';
                                    } else if (check_session() && $_SESSION['type'] === 'admin') {
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link d-inline-block fs-5\" href=\"/admin/account/\" id=\"profile_nav\">Account</a>
                                          </li>";
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link d-inline-block fs-5 text-danger text-nowrap\" href=\"/ajax_service/authentication/logout\">Sign Out</a>
                                          </li>";
                                    }
                                    ?>
                              </ul>
                        </div>
                  </div>
            </nav>
      </header>
</section>