<?php
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) header('Location: /');
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
      include('../../../layout/user/header.php');
      ?>
      <section id="page">

      </section>
      <?php
      include('../../../layout/footer.php');
      ?>
      <script src="/javascript/user/menu_after_load.user.js"></script>
</body>

</html>