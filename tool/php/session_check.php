
<?php
require_once __DIR__ . '/verify_user_id.php';

function check_session()
{
      if (session_status() !== PHP_SESSION_ACTIVE) {
            // Set the session cookie's attributes: expires - path - domain - secure - httpOnly
            // session_set_cookie_params(3 * 24 * 60 * 60, '/; samesite=' .'Strict', $_SERVER['HTTP_HOST'], true, true); // (old)
            if (!session_set_cookie_params([
                  'lifetime' => 3 * 24 * 60 * 60,
                  'path' => '/',
                  'domain' => '',
                  'secure' => true,
                  'httponly' => true,
                  'samesite' => 'Strict'
            ])) return false;

            // Start or resume session
            if (!session_start()) return false;
      }

      if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type']) && ($_SESSION['type'] === 'admin' || $_SESSION['type'] === 'customer')) {
            if (verifyUserID($_SESSION['id'], $_SESSION['type']))
                  return true;
            else {
                  session_destroy();
                  //$_SESSION = [];
                  return false;
            }
      } else return false;
}

?>