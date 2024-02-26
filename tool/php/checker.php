
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

function isInPeriod($start, $end)
{
      $today = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
      $today->setTime(0, 0, 0);
      $startDate = DateTime::createFromFormat('Y-m-d', $start);
      $endDate = DateTime::createFromFormat('Y-m-d', $end);

      if ($today < $startDate) return 2;
      elseif ($today > $endDate) return 0;
      else return 1;
}
?>