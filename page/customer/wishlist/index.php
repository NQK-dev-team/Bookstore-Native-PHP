<?php
require_once __DIR__ .'/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/role_check.php';
?>

<!DOCTYPE html>
<html>

<head>
      <?php
      require_once __DIR__ . '/../../../head_element/cdn.php';
      require_once __DIR__ . '/../../../head_element/meta.php';
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

</head>

<body>
      <?php
      require_once __DIR__ . '/../../../layout/customer/header.php';
      ?>
      <section id="page">
      </section>
      <?php
      require_once __DIR__ . '/../../../layout/footer.php';
      ?>
      <script src="/javascript/customer/menu_after_load.js"></script>
</body>

</html>