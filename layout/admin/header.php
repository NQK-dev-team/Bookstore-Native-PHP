<?php
require_once __DIR__ . '../../../tool/php/session_check.php';
?>

<section id="header">
      <header class="w-100 h-100">
            <nav class="navbar navbar-expand-lg py-auto w-100">
                  <div class="container-fluid px-0">
                        <a class="navbar-brand d-flex align-items-center ms-2" href="/admin/">
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
                                          <a class="nav-link fs-5" href="/admin/" id="home_nav">Home</a>
                                    </li>
                                    <li class="nav-item dropdown mx-2">
                                          <p class="nav-link m-0 fs-5" id="manage_dropdown_0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Manage
                                          </p>
                                          <ul class="dropdown-menu">
                                                <?php
                                                if (!check_session())
                                                      echo '<li><a id="manage_dropdown_1" class="dropdown-item" href="/admin/authentication/">Book</a></li>
                                                <li><a id="manage_dropdown_2" class="dropdown-item" href="/admin/authentication/">Category</a></li>
                                                <li><a id="manage_dropdown_3" class="dropdown-item" href="/admin/authentication/">Customer</a></li>
                                                <li><a id="manage_dropdown_4" class="dropdown-item" href="/admin/authentication/">Coupon</a></li>';
                                                else
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
                                                echo '<a id="statistic_nav" class="nav-link fs-5" href="/admin/authentication/">Statistic</a>';
                                          else
                                                echo '<a id="statistic_nav" class="nav-link fs-5" href="/admin/statistic/">Statistic</a>';
                                          ?>
                                    </li>
                                    <?php
                                    if (!check_session()) {
                                          echo '<li class="nav-item ms-2">
                                                      <a class="nav-link fs-5" href="/admin/authentication/" id="policy_nav">Policy</a>
                                                </li>';
                                          echo '<li class="nav-item ms-2">
                                                      <a class="nav-link fs-5" href="/admin/authentication/" id="signin_nav">Sign in</a>
                                                </li>';
                                    } else {
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5\" href=\"/admin/policy/\" id=\"policy_nav\">Policy</a>
                                          </li>";
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5\" href=\"/admin/account/\" id=\"profile_nav\">Account</a>
                                          </li>";
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5 text-danger text-nowrap\" href=\"/ajax_service/authentication/logout\">Sign Out</a>
                                          </li>";
                                    }
                                    ?>
                              </ul>
                        </div>
                  </div>
            </nav>
      </header>
</section>