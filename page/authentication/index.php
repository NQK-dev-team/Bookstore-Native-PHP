<?php
// Set the session cookie's attributes: expires - path - domain - secure - httpOnly
session_set_cookie_params(3 * 24 * 60 * 60, "/", "", true, true);
// Start session
session_start();
// Set session variables
//$_SESSION['id'] = 'test';
?>
<!DOCTYPE html>
<html>

<head>
      <?php
      include "../..//head_element/cdn.php";
      include "../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">
</head>

<body>
</body>

</html>