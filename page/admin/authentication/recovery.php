<?php
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) header('Location: /admin/');
?>

<!DOCTYPE html>
<html>

<head>
      <?php
      include "../../../head_element/cdn.php";
      include "../../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">


</head>

<body>
      <?php
      include('../../../layout/admin/header.php');
      ?>
      <section id="page">

      </section>
      <?php
      include('../../../layout/footer.php');
      ?>
      <script src="/javascript/admin/menu_after_load.admin.js"></script>
</body>

</html>