

<?php
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['id']) || !isset($_SESSION['type'])) {
      if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
            header('Location: /admin/authentication/');
      else
            header('Location: /authentication/');
}
?>