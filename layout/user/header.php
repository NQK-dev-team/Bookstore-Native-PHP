<section id="header">
      <header class="w-100 h-100">
            <nav class="navbar navbar-expand-lg py-auto w-100">
                  <div class="container-fluid px-0">
                        <a class="navbar-brand d-flex align-items-center ms-2" href="/">
                              <!-- <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" id="logo_img"></img> -->
                              <img src="/image/logo.png" id="logo_img"></img>
                              <p class="mb-0 ms-2">NQK Bookstore</p>
                        </a>
                        <button class="navbar-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                              <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse mt-2 mt-lg-0 me-lg-2 bg-white px-3" id="navbarSupportedContent">
                              <!-- Missing search functionality -->
                              <form class="d-flex ms-lg-3" role="search">
                                    <input id="search_book" class="form-control me-2" type="search" placeholder="Search by name, author or ISBN number" aria-label="Search">
                                    <button class="btn btn-outline-success btn-sm" type="submit">Search</button>
                              </form>
                              <ul class="navbar-nav ms-auto">
                                    <li class="nav-item mx-2">
                                          <a class="nav-link fs-5" href="/" id="home_nav">Home</a>
                                    </li>
                                    <li class="nav-item mx-2">
                                          <a class="nav-link fs-5" href="/book/" id="book_nav">Books</a>
                                    </li>
                                    <?php
                                    if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['id']) || !isset($_SESSION['type']))
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5" href="/authentication/" id="wishlist_nav">Wishlist</a>
                                                </li>';
                                    else
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5" href="/wishlist/" id="wishlist_nav">Wishlist</a>
                                                </li>';
                                    ?>
                                    <?php
                                    if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['id']) || !isset($_SESSION['type']))
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5" href="/authentication/" id="cart_nav">Cart</a>
                                                </li>';
                                    else
                                          echo '<li class="nav-item mx-2">
                                                      <a class="nav-link fs-5" href="/wishlist/" id="cart_nav">Cart</a>
                                                </li>';
                                    ?>
                                    <?php
                                    if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['id']) || !isset($_SESSION['type']))
                                          echo '<li class="nav-item ms-2">
                                                      <a class="nav-link fs-5" href="/authentication/" id="signin_nav">Sign in</a>
                                                </li>';
                                    else
                                          echo "<li class=\"nav-item ms-2\">
                                                <a class=\"nav-link fs-5\" href=\"/account/\" id=\"profile_nav\">Profile</a>
                                          </li>";
                                    ?>
                              </ul>
                        </div>
                  </div>
            </nav>
      </header>
</section>