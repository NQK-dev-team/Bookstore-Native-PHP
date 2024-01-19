
<?php

function sanitize($param, $ignore_quotes = true)
{
      if (!is_string($param)) return $param;

      // Remove leading and trailing whitespaces
      $param = trim($param);
      // Remove backslashes
      $param = stripslashes($param);
      // Convert special characters to HTML entities
      $param = htmlspecialchars($param, $ignore_quotes ? ENT_NOQUOTES : ENT_QUOTES);

      return $param;
}

?>