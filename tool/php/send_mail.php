
<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../config/phpmailler.php';

function create_new_mail($email, $code)
{
      global $mail;

      try {
            $mail->addAddress($email);

            $mail->Subject = 'Confirmation code (No Reply)';
            $mail->Body    = "This is your confirmation code <b>$code</b>";
            $mail->AltBody = "This is your confirmation code: $code";

            $mail->send();
      } catch (Exception $e) {
            $GLOBALS['error_message'] = "Message could not be sent. Error: {$mail->ErrorInfo}";
            http_response_code(500);
            require __DIR__ . '/../../error/500.php';
            // echo "Message could not be sent. Error: {$mail->ErrorInfo}";
      }
}

function recovery_mail($email, $code)
{
      global $mail;

      try {
            $mail->addAddress($email);

            $mail->Subject = 'Recovery code (No Reply)';
            $mail->Body    = "This is your recovery code <b>$code</b>";
            $mail->AltBody = "This is your recovery code: $code";

            $mail->send();
      } catch (Exception $e) {
            $GLOBALS['error_message'] = "Message could not be sent. Error: {$mail->ErrorInfo}";
            http_response_code(500);
            require __DIR__ . '/../../error/500.php';
            // echo "Message could not be sent. Error: {$mail->ErrorInfo}";
      }
}

?>