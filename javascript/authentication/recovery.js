$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#passwordChangeModal').on('hidden.bs.modal', function ()
      {
            window.location.href = "/authentication/";
      });
});

let globalEmail = null;

function enterEmail(e, user_type)
{
      e.preventDefault();

      const email = sanitize($('#inputEmail').val());

      let isOK = true;

      if (email === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_1');
            p_elem.innerHTML = 'Email field is empty!';
            const error_message = document.getElementById('recovery_fail_1');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(email) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_1');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_1');
                  error_message.style.display = 'flex';
            }
      }

      if (isOK)
      {
            $('*').addClass('wait');
            $.ajax({
                  url: '/ajax_service/authentication/get_recovery_code.php',
                  method: 'POST',
                  data: { email: email, type: sanitize(user_type) },
                  dataType: 'json',
                  success: function (data)
                  {
                        $('*').removeClass('wait');
                        if (data.error)
                        {
                              const p_elem = document.getElementById('error_message_content_1');
                              p_elem.innerHTML = data.error;
                              const error_message = document.getElementById('recovery_fail_1');
                              error_message.style.display = 'flex';
                        }
                        else if (data.query_result)
                        {
                              const p_elem = document.getElementById('error_message_content_1');
                              p_elem.innerHTML = '';
                              const error_message = document.getElementById('recovery_fail_1');
                              error_message.style.display = 'none';

                              globalEmail = email;
                              $('#recovery_email_form').css('display', 'none');
                              $('#recovery_password_form').css('display', 'none');
                              $('#recovery_code_form').css('display', 'flex');
                        }
                  },
                  error: function (err)
                  {
                        $('*').removeClass('wait');
                        console.error(err);
                        if (err.status === 500)
                        {
                              const p_elem = document.getElementById('error_message_content_1');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('recovery_fail_1');
                              error_message.style.display = 'flex';
                        } else if (err.status === 400)
                        {
                              const p_elem = document.getElementById('error_message_content_1');
                              p_elem.innerHTML = 'Server request error!';
                              const error_message = document.getElementById('recovery_fail_1');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}

function requestRecoveryCode()
{
      let isOK = true;

      if (globalEmail && globalEmail === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
            }
      }

      if (isOK)
      {
            $('*').addClass('wait');

            $.ajax({
                  url: '/ajax_service/authentication/request_recovery_code.php',
                  method: 'POST',
                  data: { email: globalEmail },
                  //dataType: 'json',
                  success: function (data)
                  {
                        $('*').removeClass('wait');
                        if (data.error)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = data.error;
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        }
                        else if (data.query_result)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = '';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'none';
                        }
                  },
                  error: function (err)
                  {
                        $('*').removeClass('wait');
                        console.error(err);
                        if (err.status === 500)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        } else if (err.status === 400)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = 'Server request error!';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}

function enterCode(e)
{
      e.preventDefault();

      const code = sanitize($('#inputRecoveryCode').val());

      let isOK = true;

      if (globalEmail && globalEmail === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
            }
      }

      if (code === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'Recovery code field is empty!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9]{8}$/;
            if (!regex.test(code) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Recovery code format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
            }
      }

      if (isOK)
            $.ajax({
                  url: '/ajax_service/authentication/check_recovery_code.php',
                  method: 'POST',
                  data: { email: globalEmail, code: code },
                  dataType: 'json',
                  success: function (data)
                  {
                        if (data.error)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = data.error;
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        }
                        else if (data.query_result)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = '';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'none';

                              $('#recovery_email_form').css('display', 'none');
                              $('#recovery_password_form').css('display', 'flex');
                              $('#recovery_code_form').css('display', 'none');
                        }
                  },
                  error: function (err)
                  {
                        console.error(err);
                        if (err.status === 500)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        } else if (err.status === 400)
                        {
                              const p_elem = document.getElementById('error_message_content_2');
                              p_elem.innerHTML = 'Server request error!';
                              const error_message = document.getElementById('recovery_fail_2');
                              error_message.style.display = 'flex';
                        }
                  }
            });
}

function changePassword(e, user_type)
{
      e.preventDefault();

      const password = sanitize($('#inputNewPassword').val());
      const confirmPassword = sanitize($('#inputConfirmNewPassword').val());

      let isOK = true;

      if (globalEmail && globalEmail === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
            }
      }

      if (password === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'New password field is empty!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
      }
      else if (password.length < 8 && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'New password must be at least 8 characters!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_3');
                  p_elem.innerHTML = 'New password must contain at least one uppercase letter, one lowercase letter, one number and one special character!';
                  const error_message = document.getElementById('recovery_fail_3');
                  error_message.style.display = 'flex';
            }
      }

      if (confirmPassword === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'Confirm new password field is empty!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
      }
      else if (confirmPassword.length < 8 && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'Confirm password must be at least 8 characters!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(confirmPassword) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content_3');
                  p_elem.innerHTML = 'Confirm password must contain at least one uppercase letter, one lowercase letter, one number and one special character!';
                  const error_message = document.getElementById('recovery_fail_3');
                  error_message.style.display = 'flex';
            }
      }

      if (password !== confirmPassword && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'Passwords are not matched!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
      }

      if (isOK)
      {
            $('*').addClass('wait');

            $.ajax({
                  url: '/ajax_service/authentication/change_password.php',
                  method: 'POST',
                  data: { email: globalEmail, password: password, confirmPassword: confirmPassword, type: sanitize(user_type) },
                  dataType: 'json',
                  success: function (data)
                  {
                        $('*').removeClass('wait');
                        if (data.error)
                        {
                              const p_elem = document.getElementById('error_message_content_3');
                              p_elem.innerHTML = data.error;
                              const error_message = document.getElementById('recovery_fail_3');
                              error_message.style.display = 'flex';
                        }
                        else if (data.query_result)
                        {
                              const p_elem = document.getElementById('error_message_content_3');
                              p_elem.innerHTML = '';
                              const error_message = document.getElementById('recovery_fail_3');
                              error_message.style.display = 'none';

                              $('#passwordChangeModal').modal('show');
                        }
                  },
                  error: function (err)
                  {
                        $('*').removeClass('wait');
                        console.error(err);
                        if (err.status === 500)
                        {
                              const p_elem = document.getElementById('error_message_content_3');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('recovery_fail_3');
                              error_message.style.display = 'flex';
                        } else if (err.status === 400)
                        {
                              const p_elem = document.getElementById('error_message_content_3');
                              p_elem.innerHTML = 'Server request error!';
                              const error_message = document.getElementById('recovery_fail_3');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}

function changeEmail()
{
      globalEmail = null;

      {
            const p_elem = document.getElementById('error_message_content_1');
            p_elem.innerHTML = '';
            const error_message = document.getElementById('recovery_fail_1');
            error_message.style.display = 'none';
            $('#recovery_email_form').css('display', 'flex');
      }

      {
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = '';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'none';
            $('#recovery_password_form').css('display', 'none');
      }

      {
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = '';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'none';
            $('#recovery_code_form').css('display', 'none');
      }

      $('input').val('');
}