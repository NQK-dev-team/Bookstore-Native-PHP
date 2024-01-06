
<?php
require_once __DIR__ . '/../../config/phpmailler.php';

function create_new_account_mail($email)
{
      global $mail;

      $mail->addAddress($email);

      $mail->Subject = 'Account created!';
      $mail->Body    = "You account has been created successfully, you can now use this email address to login NQK Bookstore website!";
      $mail->AltBody = "You account has been created successfully, you can now use this email address to login NQK Bookstore website!";

      $mail->send();
}

function recovery_mail($email, $code)
{
      global $mail;

      $mail->addAddress($email);

      $mail->Subject = 'Recovery code (No Reply)';
      $mail->Body    = "This is your recovery code <b>$code</b>";
      $mail->AltBody = "This is your recovery code: $code";

      $mail->send();
}

?>