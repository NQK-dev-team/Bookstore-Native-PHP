
<?php
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type'])) {
      if ($_SESSION['type'] === 'admin') {
            if (!str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                  http_response_code(400);
                  include __DIR__ . '/../error/400.php';
            }
      } else if ($_SESSION['type'] === 'user') {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin')) {
                  http_response_code(403);
                  include __DIR__ . '/../error/403.php';
            }
      }
}
?>