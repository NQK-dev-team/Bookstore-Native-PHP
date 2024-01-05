<?php
require_once __DIR__ . '../../../../tool/php/session_check.php';

if (!check_session()) {
      // Set the session cookie's attributes: expires - path - domain - secure - httpOnly
      session_set_cookie_params(3 * 24 * 60 * 60, "/", "", true, true);
      // Start session
      session_start();
} else {
      header('Location: /');
}
?>

<!DOCTYPE html>
<html>

<head>
      <?php
      require_once __DIR__ . "/../../../head_element/cdn.php";
      require_once __DIR__ . "/../../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="Login to NQK bookstore">
      <title>Login</title>
      <link rel="stylesheet" href="/css/authentication/login.css">
</head>

<body>
      <?php
      require_once __DIR__ . '/../../../layout/user/header.php';
      ?>
      <section id="page">
            <div class="container-fluid h-100 d-flex justify-content-center py-4">
                  <form class="bg-white border border-black rounded login_form my-auto d-flex flex-column px-3" id="login_form">
                        <div class='w-100 d-flex flex-column'>
                              <h2 class="mx-auto mb-0 mt-1">Login</h2>
                              <div class="align-items-center justify-content-center w-100 error_message mt-2 mx-auto" id="login_fail">
                                    <svg class="ms-1" fill="#ff0000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000" stroke-width="30.72">
                                          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                          <g id="SVGRepo_iconCarrier">
                                                <path d="M520.741 163.801a10.234 10.234 0 00-3.406-3.406c-4.827-2.946-11.129-1.421-14.075 3.406L80.258 856.874a10.236 10.236 0 00-1.499 5.335c0 5.655 4.585 10.24 10.24 10.24h846.004c1.882 0 3.728-.519 5.335-1.499 4.827-2.946 6.352-9.248 3.406-14.075L520.742 163.802zm43.703-26.674L987.446 830.2c17.678 28.964 8.528 66.774-20.436 84.452a61.445 61.445 0 01-32.008 8.996H88.998c-33.932 0-61.44-27.508-61.44-61.44a61.445 61.445 0 018.996-32.008l423.002-693.073c17.678-28.964 55.488-38.113 84.452-20.436a61.438 61.438 0 0120.436 20.436zM512 778.24c22.622 0 40.96-18.338 40.96-40.96s-18.338-40.96-40.96-40.96-40.96 18.338-40.96 40.96 18.338 40.96 40.96 40.96zm0-440.32c-22.622 0-40.96 18.338-40.96 40.96v225.28c0 22.622 18.338 40.96 40.96 40.96s40.96-18.338 40.96-40.96V378.88c0-22.622-18.338-40.96-40.96-40.96z"></path>
                                          </g>
                                    </svg>
                                    <p class="mb-0 text-danger fw-medium ms-2" id="error_message_content"></p>
                              </div>
                              <hr>
                        </div>
                        <div class="form-group">
                              <div class="d-flex">
                                    <label for="inputEmail" class="fs-4 fw-medium">Email</label>
                                    <a href="sign_up" class="text-decoration-none ms-auto py-1">Sign up?</a>
                              </div>
                              <input type=" email" required class="form-control" id="inputEmail" placeholder="Enter email" name="email" pattern='[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$'>
                        </div>
                        <div class="form-group mt-3">
                              <label for="inputPassword" class="fs-4 fw-medium">Password</label>
                              <input type="password" required class="form-control" id="inputPassword" placeholder="Enter password" name="password" minlength="8">
                        </div>
                        <a class="mx-auto mt-2 text-primary text-decoration-none" href="recovery">Forgot password?</a>
                        <div class="mt-auto my-3 mx-auto">
                              <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                  </form>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../../../layout/footer.php';
      ?>
      <script src="/javascript/user/menu_after_load.js"></script>
      <script src="/tool/js/sanitizer.js"></script>
      <script src="/javascript/authentication/login.js"></script>
</body>

</html>