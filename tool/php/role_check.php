
<?php

require_once __DIR__ . '/session_check.php';

function return_navigate_error()
{
      if (check_session()) {
            if ($_SESSION['type'] === 'admin') {
                  if (!str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                        return 400;
                  }
            } else if ($_SESSION['type'] === 'customer') {
                  if (str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                        return 403;
                  }
            }
      }
      return 200;
}
?>