<?php
require __DIR__ .'/../../../tool/php/login_check.php';
require __DIR__ . '/../../../tool/php/role_check.php';
?>

<!DOCTYPE html>
<html>

<head>
      <?php
      require __DIR__ . '/../../../head_element/cdn.php';
      require __DIR__ . '/../../../head_element/meta.php';
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

</head>

<body>
      <?php
      require __DIR__ . '/../../../layout/user/header.php';
      ?>
      <section id="page">
      </section>
      <?php
      require __DIR__ . '/../../../layout/footer.php';
      ?>
      <script src="/javascript/user/menu_after_load.js"></script>
</body>

</html>