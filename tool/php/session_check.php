
<?php
function check_session()
{
      if (session_status() !== PHP_SESSION_ACTIVE) {
            // Set the session cookie's attributes: expires - path - domain - secure - httpOnly
            session_set_cookie_params(3 * 24 * 60 * 60, "/", "", true, true);
            // Start or resume session
            session_start();
      }
      return session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type']);
}

?>