
<?php
require_once __DIR__ . '/verify_user_id.php';

function check_session()
{
      if (session_status() !== PHP_SESSION_ACTIVE) {
            // Set the session cookie's attributes: expires - path - domain - secure - httpOnly
            if (!session_set_cookie_params(3 * 24 * 60 * 60, "/", "", true, true)) return false;
            // Start or resume session
            if (session_start()) return false;
      }

      if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type']) && ($_SESSION['type'] === 'admin' || $_SESSION['type'] === 'customer')) return verifyUserID($_SESSION['id']);
      else return false;
}

?>