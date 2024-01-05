function loginHandler(e, user_type)
{
      e.preventDefault();

      const email = sanitize(document.getElementById('inputEmail').value);
      const password = sanitize(document.getElementById('inputPassword').value);

      $.ajax({
            url: '/ajax_service/authentication/login_handler.php',
            method: 'POST',
            data: { email: email, password: password, type: user_type },
            dataType: 'json',
            success: function (data)
            {
                  if (data.error)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = data.error;
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        if (user_type === 'customer')
                              window.location.href = '/';
                        if (user_type === 'admin')
                              window.location.href = '/admin/';
                  }
            },
            error: function (err)
            {
                  console.error(err);
                  if (err.status === 500)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = 'Server encountered error!';
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  } else if (err.status === 400)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = 'Server request error!';
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  }
            }
      });
}