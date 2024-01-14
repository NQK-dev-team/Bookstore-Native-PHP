
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

function formatISBN($isbn)
{
      // Remove any existing hyphens or spaces
      $isbn = str_replace(['-', ' '], '', $isbn);

      // Check if the ISBN is 13 digits long
      if (strlen($isbn) == 13) {
            // Format as 3-5-2-3-1
            return substr($isbn, 0, 3) . '-' . substr($isbn, 3, 1) . '-' . substr($isbn, 4, 2) . '-' . substr($isbn, 6, 6) . '-' . substr($isbn, 12, 1);
      }

      // Invalid ISBN length
      return "Invalid ISBN length";
}

function convertDateFormat($inputDate)
{
      // Create a DateTime object from the input date
      $dateTime = new DateTime($inputDate);

      // Format the date as "Month day, year"
      $formattedDate = $dateTime->format("F j, Y");

      return $formattedDate;
}

function revertEncodedCharacter($input)
{
      $result = str_replace('%20', ' ', $input);
      $result = str_replace('%60', '`', $result);

      return $result;
}
?>