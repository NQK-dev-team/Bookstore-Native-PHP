
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

# Return input type=hidden with the anti-CSRF token
function csrfInput($idx = null)
{
      if (check_session()) {
            if ($idx)
                  return '<input type="hidden" name="csrf_token_' . $idx . '" value="' . $_SESSION['csrf_token'] . '">';
            else
                  return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
      }
}
?>