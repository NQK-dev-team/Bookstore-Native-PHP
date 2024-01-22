
<?php
require_once __DIR__ . '/session_check.php';

# Generate a random anti-CSRF token
function generateToken()
{
      if (check_session())
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

# Check if the anti-CSRF token is valid
function checkToken($token)
{
      return check_session() && isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}

# Store anti-CSRF token in a constant variable in javascript
function storeToken()
{
      if (check_session() && isset($_SESSION['csrf_token'])) {
            echo '<script>const CSRF_TOKEN = "' . $_SESSION['csrf_token'] . '";</script>';
      }
}

?>