$(document).ready(function ()
{ 
      $('#inputEmail').focus();
});

function loginHandler(e, user_type)
{
      e.preventDefault();

      const email = encodeData(document.getElementById('inputEmail').value);
      const password = encodeData(document.getElementById('inputPassword').value);
      const type = encodeData(user_type);

      if (email === '')
      {
            reportCustomValidity($('#inputEmail').get(0), "Email field is empty!");
            return;
      }

      if (password === '')
      {
            reportCustomValidity($('#inputPassword').get(0), "Password field is empty!");
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/login_handler.php',
            method: 'POST',
            data: { email: email, password: password, type: type },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content').text(data.error);
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content').text('');
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'none';

                        if (type === 'customer')
                              window.location.href = '/';
                        if (type === 'admin')
                              window.location.href = '/admin/';
                  }
            },
            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  
                  if (err.status >= 500)
                  {
                        $('#error_message_content').text('Server encountered error!');
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content').text(err.responseJSON.error);
                        const error_message = document.getElementById('login_fail');
                        error_message.style.display = 'flex';
                  }
            }
      });
}