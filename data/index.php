
<?php
require_once __DIR__ . '../../tool/php/session_check.php';


if (check_session()) {
} else {
      http_response_code(403);
      require_once __DIR__ . '../../error/403.php';
}
?>