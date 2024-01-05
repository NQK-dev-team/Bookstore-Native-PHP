const form = document.getElementById('login_form');

function loginHandler(e, type)
{
      e.preventDefault();

      console.log(type);      

      const email = sanitize(document.getElementById('inputEmail').value);
      const password = sanitize(document.getElementById('inputPassword').value);
      console.log(email);
      $.ajax({
            url: '/ajax_service/authentication/login_handler.php',
            method: 'POST',
            data: { email: email, password: password },
            dataType: 'json',
            success: function (data)
            {
                  console.log(data);
                  if (data.error)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = data.error;
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  }
                  // Need to make a query to the database to continue or not
            },
            error: function (err)
            {
                  console.error(err);
            }
      });
}

form.addEventListener('submit', loginHandler);