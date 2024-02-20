
<?php
function formatISBN($isbn)
{
      // Remove any existing hyphens or spaces
      $isbn = str_replace(['-', ' '], '', $isbn);

      // Check if the ISBN is 13 digits long
      if (strlen($isbn) == 13) {
            // Format as 3-1-2-6-1
            return substr($isbn, 0, 3) . '-' . substr($isbn, 3, 1) . '-' . substr($isbn, 4, 2) . '-' . substr($isbn, 6, 6) . '-' . substr($isbn, 12, 1);
      }

      // Invalid ISBN number
      return "Invalid ISBN number";
}

function MDYDateFormat($inputDate)
{
      // Create a DateTime object from the input date
      $dateTime = new DateTime($inputDate);

      // Format the date as "Month day, year"
      $formattedDate = $dateTime->format("F j, Y");

      return $formattedDate;
}

function splitOrderCode($str)
{
      // Split the string into chunks of 4 characters
      $chunks = chunk_split($str, 4, '-');

      // Remove the trailing hyphen
      $chunks = rtrim($chunks, '-');

      return $chunks;
}

function formatOrderTime($inputTime)
{
      // Create a DateTime object from the input date
      $dateTime = new DateTime($inputTime);

      // Format the date as "Month day, year"
      $formattedTime = $dateTime->format("F j, Y H:i:s");

      return $formattedTime;
}
?>