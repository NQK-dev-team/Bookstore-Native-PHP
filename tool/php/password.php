
<?php

// This file is only created for better clarity, you don't have to use these functions if you want

// Salt (combined with the password to use when verifying is generated randomly in this function)
function hash_password($password)
{
      return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($raw_password, $hased_password)
{
      return password_verify($raw_password, $hased_password);
}

?>