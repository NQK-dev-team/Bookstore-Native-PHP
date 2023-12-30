<!DOCTYPE html>
<html>

<head>
      <?php
      include __DIR__ . "/../head_element/cdn.php";
      include __DIR__ . "/../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="Server Internal Error!">
      <title>Error 500</title>
</head>

<body>
      <?php
      if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) {
            if ($_SESSION['type'] === 'admin')
                  include __DIR__ . '/../layout/admin/header.php';
            else if ($_SESSION['type'] === 'user')
                  include __DIR__ . '/../layout/user/header.php';
      } else {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
                  include __DIR__ . '/../layout/admin/header.php';
            else
                  include __DIR__ . '/../layout/user/header.php';
      }
      ?>
      <section id="page">
            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <h1>Error 500</h1>
                  <h5 class="text-center">The server has encountered an error!</h5>
                  <?php
                  if (isset($GLOBALS['error_message']))
                        echo "<p class=\"text-center text-wrap text-truncate w-75\">{$GLOBALS['error_message']}</p>";
                  ?>
            </div>
      </section>
      <?php
      include __DIR__ . '/../layout/footer.php';
      ?>
</body>

</html>