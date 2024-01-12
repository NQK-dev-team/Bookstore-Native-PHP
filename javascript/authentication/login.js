function loginHandler(e, user_type)
{
      e.preventDefault();

      const email = sanitize(document.getElementById('inputEmail').value);
      const password = sanitize(document.getElementById('inputPassword').value);

      if (email === '')
      {
            const elem = document.getElementById('inputEmail');
            elem.setCustomValidity("Email field is empty!");
            clearCustomValidity(elem);
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(email))
            {
                  const elem = document.getElementById('inputEmail');
                  elem.setCustomValidity("Email format invalid!");
                  clearCustomValidity(elem);
                  return;
            }
      }

      if (password === '')
      {
            const elem = document.getElementById('inputPassword');
            elem.setCustomValidity("Password field is empty!");
            clearCustomValidity(elem);
            return;
      }
      else if (password.length < 8)
      {
            const elem = document.getElementById('inputPassword');
            elem.setCustomValidity("Password must be at least 8 characters!");
            clearCustomValidity(elem);
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password))
            {
                  const elem = document.getElementById('inputPassword');
                  elem.setCustomValidity("Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!");
                  clearCustomValidity(elem);
                  return;
            }
      }

      $.ajax({
            url: '/ajax_service/authentication/login_handler.php',
            method: 'POST',
            data: { email: email, password: password, type: sanitize(user_type) },
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
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = '';
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'none';

                        if (sanitize(user_type) === 'customer')
                              window.location.href = '/';
                        if (sanitize(user_type) === 'admin')
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