
<?php
require_once __DIR__ . '/../../tool/php/session_check.php';

if (!check_session()) {
      http_response_code(400);
      require_once __DIR__ . '/../../error/400.php';
      exit;
} else {
      if ($_SESSION['type'] === 'admin') {
            session_destroy();
            session_commit();
            header('Location: /admin/authentication/');
      } else if ($_SESSION['type'] === 'customer') {
            session_destroy();
            session_commit();
            header('Location: /');
      }
}
?>