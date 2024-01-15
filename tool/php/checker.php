
<?php
function isAgeValid($input)
{
      // Assuming $input is the date of birth in 'Y-m-d' format
      $dob = new DateTime($input, new DateTimeZone('Asia/Ho_Chi_Minh'));
      $today = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
      $age = $today->format('Y') - $dob->format('Y');

      // Check if the birthday has occurred this year
      if ($today->format('m') < $dob->format('m') || ($today->format('m') == $dob->format('m') && $today->format('d') < $dob->format('d'))) {
            $age--;
      }

      return $age >= 18;
}
?>