<?php
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) header('Location: /admin/');
?>

<!DOCTYPE html>
<html>

<head>
      <?php
      require __DIR__ . "/../../../head_element/cdn.php";
      require __DIR__ . "/../../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">


</head>

<body>
      <?php
      require __DIR__ . '/../../../layout/admin/header.php';
      ?>
      <section id="page">

      </section>
      <?php
      require __DIR__ . '/../../../layout/footer.php';
      ?>
      <script src="/javascript/admin/menu_after_load.js"></script>
</body>

</html>