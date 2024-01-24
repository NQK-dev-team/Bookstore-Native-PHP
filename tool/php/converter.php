
<?php
function convertToOrdinal($number)
{
      if (!is_numeric($number) || $number < 1) {
            return "Error!";
      }

      $lastDigit = $number % 10;
      $secondLastDigit = floor(($number % 100) / 10);

      if ($secondLastDigit == 1) {
            $suffix = "th";
      } else {
            switch ($lastDigit) {
                  case 1:
                        $suffix = "st";
                        break;
                  case 2:
                        $suffix = "nd";
                        break;
                  case 3:
                        $suffix = "rd";
                        break;
                  default:
                        $suffix = "th";
            }
      }

      return $number . $suffix;
}

function normalizeURL($url)
{
      $url = str_replace('%2F', '/', $url);

      return $url;
}
?>