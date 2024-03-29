
<?php
require_once __DIR__ . '/../../config/phpmailler.php';

function create_new_account_mail($email)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Account Created!';
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
            $mail->clearAllRecipients();
            $mail->addAddress($refEmail);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Referrer Acknowledge';
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
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Recovery Code (No Reply)';
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
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Password Changed!';
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

function deactivate_mail($email)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Account Deactivated!';
      $mail->Body    = "You account has been deactivated. To reactivate it, simply logining in!";
      $mail->AltBody = "You account has been deactivated. To reactivate it, simply logining in!";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function activate_mail($email)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Account Reactivated!';
      $mail->Body    = "You account has been reactivated, happy shopping!";
      $mail->AltBody = "You account has been reactivated, happy shopping!";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function delete_mail($email, $type)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      if ($type === 1) {
            $mail->Subject = 'Account Delete Request Submitted!';
            $mail->Body    = "You account has been deactivated and will be deleted in 14 days, you can cancel the process simply by loging back in those upcoming days!";
            $mail->AltBody = "You account has been deactivated and will be deleted in 14 days, you can cancel the process simply by loging back in those upcoming days!";
      } else if ($type === 2) {
            $mail->Subject = 'Account Deleted!';
            $mail->Body    = "You account has been deleted, come back again soon!";
            $mail->AltBody = "You account has been deleted, come back again soon!";
      }

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function delete_cancel_mail($email)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = 'Account Delete Cancel!';
      $mail->Body    = "You account delete process has been cancelled, happy shopping!";
      $mail->AltBody = "You account delete process has been cancelled, happy shopping!";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

function discount_notify($email, $eventName, $discount, $type, $books)
{
      global $mail;

      try {
            $mail->clearAllRecipients();
            $mail->addAddress($email);
      } catch (Exception $e) {
            throw new Exception('Failed to add address: ' . $e->getMessage());
      }

      $mail->Subject = "{$eventName} is here!";

      if ($type === 1) {
            $mail->Body = "<strong>{$discount}% sales for all books, hurry up and grab what you want!</strong>";
            $mail->AltBody = "{$discount}% sales for all books, hurry up and grab what you want!";
      } else if ($type === 2) {
            $mail->Body = "<strong>{$discount}% sales for these following books</strong><br>";
            $mail->AltBody = "{$discount}% sales for these following books\n";
      }

      if($type===2)
      {
            foreach ($books as $book) {
                  $mail->Body .= "<p>{$book}</p>";
                  $mail->AltBody .= "{$book}\n";
            }
            $mail->Body .= "<br>";
            $mail->AltBody .= "\n";
      }

      $mail->Body.="<p>Happy Shopping!</p>";
      $mail->AltBody .= "Happy Shopping!\n";

      if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
      }
}

?>