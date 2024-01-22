
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

# Echo anti-CSRF token inside the input type=hidden tag
function csrfInput()
{
      if (check_session()) {
            echo '<input type="hidden" id="csrf_token" value="' . $_SESSION['csrf_token'] . '" disabled>';
      }
}
?>