<!DOCTYPE html>
<html lang="en">

<head>
      <?php
      require_once __DIR__ . "/../head_element/cdn.php";
      require_once __DIR__ . "/../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="Page not found!">
      <title>Error 404</title>
</head>

<body>
      <?php
      require_once __DIR__ . '/../tool/php/session_check.php';

      if (check_session()) {
            if ($_SESSION['type'] === 'admin')
                  require_once __DIR__ . '/../layout/admin/header.php';
            else if ($_SESSION['type'] === 'customer')
                  require_once __DIR__ . '/../layout/customer/header.php';
      } else {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
                  require_once __DIR__ . '/../layout/admin/header.php';
            else
                  require_once __DIR__ . '/../layout/customer/header.php';
      }
      ?>
      <section id="page">
            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <h1>Error 404</h1>
                  <h5 class="text-center">The page you are looking for is not found!</h5>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../layout/footer.php';
      ?>
</body>

</html>