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

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/get_recovery_code.php',
            method: 'POST',
            data: { email: email, type: sanitize(user_type) },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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
                  $('button, a, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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

function requestRecoveryCode()
{

      if (globalEmail && globalEmail === '')
      {
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail))
            {
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
                  return;
            }
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/request_recovery_code.php',
            method: 'POST',
            data: { email: globalEmail },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
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
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
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

function enterCode(e)
{
      e.preventDefault();

      const code = sanitize($('#inputRecoveryCode').val());

      if (globalEmail && globalEmail === '')
      {
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail))
            {
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
                  return;
            }
      }

      if (code === '')
      {
            const elem = document.getElementById('inputRecoveryCode');
            elem.setCustomValidity("Recovery code field is empty!");
            clearCustomValidity(elem);
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9]{8}$/;
            if (!regex.test(code))
            {
                  const elem = document.getElementById('inputRecoveryCode');
                  elem.setCustomValidity("Recovery code format invalid!");
                  clearCustomValidity(elem);
                  return;
            }
      }

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

      if (globalEmail && globalEmail === '')
      {
            const p_elem = document.getElementById('error_message_content_2');
            p_elem.innerHTML = 'No email provided!';
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(globalEmail))
            {
                  const p_elem = document.getElementById('error_message_content_2');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('recovery_fail_2');
                  error_message.style.display = 'flex';
                  return;
            }
      }

      if (password === '')
      {
            const elem = document.getElementById('inputNewPassword');
            elem.setCustomValidity("New password field is empty!");
            clearCustomValidity(elem);
            return;
      }
      else if (password.length < 8)
      {
            const elem = document.getElementById('inputNewPassword');
            elem.setCustomValidity("New password must be at least 8 characters!");
            clearCustomValidity(elem);
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password))
            {
                  const elem = document.getElementById('inputNewPassword');
                  elem.setCustomValidity("New password must contain at least one uppercase letter, one lowercase letter, one number and one special character!");
                  clearCustomValidity(elem);
                  return;
            }
      }

      if (confirmPassword === '')
      {
            const elem = document.getElementById('inputConfirmNewPassword');
            elem.setCustomValidity("Confirm new password field is empty!");
            clearCustomValidity(elem);
            return;
      }
      else if (confirmPassword.length < 8)
      {
            const elem = document.getElementById('inputConfirmNewPassword');
            elem.setCustomValidity("Confirm password must be at least 8 characters!");
            clearCustomValidity(elem);
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(confirmPassword))
            {
                  const elem = document.getElementById('inputConfirmNewPassword');
                  elem.setCustomValidity("Confirm password must contain at least one uppercase letter, one lowercase letter, one number and one special character!");
                  clearCustomValidity(elem);
                  return;
            }
      }

      if (password !== confirmPassword)
      {
            const p_elem = document.getElementById('error_message_content_3');
            p_elem.innerHTML = 'Passwords are not matched!';
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'flex';
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/change_password.php',
            method: 'POST',
            data: { email: globalEmail, password: password, confirmPassword: confirmPassword, type: sanitize(user_type) },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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