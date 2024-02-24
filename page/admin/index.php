<?php
require_once __DIR__ . '/../../tool/php/login_check.php';
require_once __DIR__ . '/../../tool/php/role_check.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../error/403.php';
} else if ($return_status_code === 200) {
      unset($_SESSION['update_book_id']);
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../head_element/cdn.php';
            require_once __DIR__ . '/../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Nghia Duong">
            <meta name="description" content="Home page of NQK bookstore">
            <title>NQK Shop</title>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/admin/header.php';
            ?>
            <section id="page">
                  <div>
                        <canvas id="myChart"></canvas>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>