
<?php
require_once __DIR__ . '/../../config/phpmailler.php';

function create_new_account_mail($email)
{
      global $mail;

      try {
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Account created!';
      $mail->Body    = "You account has been created successfully, you can now use this email address to login NQK Bookstore website!";
      $mail->AltBody = "You account has been created successfully, you can now use this email address to login NQK Bookstore website!";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function referrer_mail($refEmail, $email)
{
      global $mail;

      try {
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Referrer acknowledge';
      $mail->Body    = "You account has been set to be the referrer of user <strong>{$email}</strong>!";
      $mail->AltBody = "You account has been set to be the referrer of user {$email}!";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function recovery_mail($email, $code)
{
      global $mail;

      try {
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Recovery code (No Reply)';
      $mail->Body    = "This is your recovery code: <b>$code</b>
      <br>
      This code will only be valid for 2 minutes";
      $mail->AltBody = "This is your recovery code: $code\n
      This code will only be valid for 2 minutes";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function change_password_mail($email, $user_type)
{
      global $mail;

      try {
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Password changed!';
      if ($user_type === 'customer') {
            $mail->Body    = "Your password has been changed successfully. If you did not perform this action, contact an admin immediately!";
            $mail->AltBody = "Your password has been changed successfully. If you did not perform this action, contact an admin immediately!";
      } else if ($user_type === 'admin') {
            $mail->Body    = "Your password has been changed successfully. If you did not perform this action, contact the database administrator immediately!";
            $mail->AltBody = "Your password has been changed successfully. If you did not perform this action, contact the database administrator immediately!";
      }

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

?>