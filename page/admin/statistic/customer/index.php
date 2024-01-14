<?php
require_once __DIR__ . '/../../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../../../error/403.php';
} else {
?>

      <!DOCTYPE html>
      <html>

      <head>
            <?php
            require_once __DIR__ . '/../../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Nghia Duong">
            <meta name="description" content="Get statistics about books of NQK Bookstore">
            <title>Customer Statistics</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../../layout/admin/header.php';
            ?>
            <section id="page">
            </section>
            <?php
            require_once __DIR__ . '/../../../../layout/footer.php';
            ?>
            <script src="/javascript/admin/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>