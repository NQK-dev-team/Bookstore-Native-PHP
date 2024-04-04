<!DOCTYPE html>
<html lang="en">

<head>
      <?php
      require_once __DIR__ . "/../../head_element/cdn.php";
      require_once __DIR__ . "/../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="NQK Bookstore discount program">
      <title>Discount Program</title>
</head>

<body>
      <?php
      require_once __DIR__ . '/../../tool/php/session_check.php';

      if (check_session()) {
            if ($_SESSION['type'] === 'admin')
                  require_once __DIR__ . '/../../layout/admin/header.php';
            else if ($_SESSION['type'] === 'customer')
                  require_once __DIR__ . '/../../layout/customer/header.php';
      } else {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
                  require_once __DIR__ . '/../../layout/admin/header.php';
            else
                  require_once __DIR__ . '/../../layout/customer/header.php';
      }
      ?>
      <section id="page">
            <div class='container-fluid d-flex flex-column'>
                  <h1 class='mt-2 mx-auto text-center'>DISCOUNT PROGRAM</h1>
                  <hr>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../../layout/footer.php';
      ?>
</body>

</html>