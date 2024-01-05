<!DOCTYPE html>
<html>

<head>
      <?php
      require_once __DIR__ . "/../head_element/cdn.php";
      require_once __DIR__ . "/../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="Resource forbidden!">
      <title>Error 400</title>
</head>

<body>
      <?php
      if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) {
            if ($_SESSION['type'] === 'admin')
                  require_once __DIR__ . '/../layout/admin/header.php';
            else if ($_SESSION['type'] === 'user')
                  require_once __DIR__ . '/../layout/user/header.php';
      } else {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
                  require_once __DIR__ . '/../layout/admin/header.php';
            else
                  require_once __DIR__ . '/../layout/user/header.php';
      }
      ?>
      <section id="page">
            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <h1>Error 400</h1>
                  <h5 class="text-center">Bad request!</h5>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../layout/footer.php';
      ?>
</body>

</html>