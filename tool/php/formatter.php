
<?php
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

function MDYDateFormat($inputDate)
{
      // Create a DateTime object from the input date
      $dateTime = new DateTime($inputDate);

      // Format the date as "Month day, year"
      $formattedDate = $dateTime->format("F j, Y");

      return $formattedDate;
}
?>