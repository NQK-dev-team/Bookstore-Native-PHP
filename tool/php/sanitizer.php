
<?php

function sanitize($param)
{
      if (!is_string($param)) return $param;
      
      // Remove leading and trailing whitespaces
      $param = trim($param);
      // Remove backslashes
      $param = stripslashes($param);
      // Convert special characters to HTML entities
      $param = htmlspecialchars($param);

      return $param;
}

?>