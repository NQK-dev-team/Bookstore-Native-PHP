
<?php

function check_session()
{
      return session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id']) && isset($_SESSION['type']);
}

?>