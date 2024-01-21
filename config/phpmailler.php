
<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
      // Server settings
      $mail->isSMTP(); // Send using SMTP
      $mail->SMTPDebug = $_ENV['SMTP_DEBUG'] ? $_ENV['SMTP_DEBUG'] : SMTP::DEBUG_OFF; // Enable verbose debug output
      $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
      $mail->SMTPAuth = true; // Enable SMTP authentication
      $mail->Username = $_ENV['SMTP_USER']; // SMTP username
      $mail->Password = $_ENV['SMTP_PASSWORD']; // SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
      $mail->Port = 587; // TCP port to connect to; use 465 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_SMTPS`

      // Mail sender
      $mail->setFrom($_ENV['SMTP_USER'], 'NQK Bookstore');

      // Mail format
      $mail->isHTML(true); //Set email format to HTML
} catch (Exception $e) {
      http_response_code(500);
      throw new Exception("Mailer configuration step failed. Error: {$mail->ErrorInfo}");
}
?>