
<?php

require __DIR__ . '/session_check.php';

if (check_session()) {
      if ($_SESSION['type'] === 'admin') {
            if (!str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                  http_response_code(400);
                  require __DIR__ . '/../error/400.php';
            }
      } else if ($_SESSION['type'] === 'user') {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                  http_response_code(403);
                  require __DIR__ . '/../error/403.php';
            }
      }
}
?>