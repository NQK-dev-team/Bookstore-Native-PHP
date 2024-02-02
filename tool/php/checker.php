
<?php
// Include Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function isAgeValid($input)
{
      // Assuming $input is the date of birth in 'Y-m-d' format
      $dob = new DateTime($input, new DateTimeZone($_ENV['TIMEZONE']));
      $today = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
      $dob->setTime(0, 0, 0); // Set time to 00:00:00
      $today->setTime(0, 0, 0); // Set time to 00:00:00
      $age = $today->format('Y') - $dob->format('Y');

      // Check if the birthday has occurred this year
      if ($today->format('m') < $dob->format('m') || ($today->format('m') == $dob->format('m') && $today->format('d') < $dob->format('d'))) {
            $age--;
      }

      return $age >= 18;
}
?>