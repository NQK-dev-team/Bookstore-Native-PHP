

<?php

require __DIR__ . '/session_check.php';

if (!check_session()) {
      if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
            header('Location: /admin/authentication/');
      else
            header('Location: /authentication/');
}
?>