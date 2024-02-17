$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#passwordChangeModal').on('hidden.bs.modal', function ()
      {
            window.location.href = "/authentication/";
      });

      initToolTip();

      $('#inputEmail').focus();
});

let globalEmail = null;

function enterEmail(e, user_type)
{
      e.preventDefault();

      const email = encodeData($('#inputEmail').val());

      if (email === '')
      {
            reportCustomValidity($('#inputEmail').get(0), "Email field is empty!");
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/get_recovery_code.php',
            method: 'POST',
            data: { email: email, type: encodeData(user_type) },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content_1').text(ata.error);
                        const error_message = document.getElementById('recovery_fail_1');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content_1').text('');
                        const error_message = document.getElementById('recovery_fail_1');
                        error_message.style.display = 'none';

                        globalEmail = email.replace(/%40/g, '@');
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
                  if (err.status >= 500)
                  {
                        $('#error_message_content_1').text('Server encountered error!');
                        const error_message = document.getElementById('recovery_fail_1');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content_1').text(err.responseJSON.error);
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
            $('#error_message_content_2').text('No email provided!');
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/request_recovery_code.php',
            method: 'POST',
            data: { email: encodeData(globalEmail) },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content_2').text(data.error);
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content_2').text('');
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
                  if (err.status >= 500)
                  {
                        $('#error_message_content_2').text('Server encountered error!');
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content_2').text(err.responseJSON.error);
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  }
            }
      });
}

function enterCode(e)
{
      e.preventDefault();

      const code = encodeData($('#inputRecoveryCode').val());

      if (globalEmail && globalEmail === '')
      {
            $('#error_message_content_2').text('No email provided!');
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }

      if (code === '')
      {
            reportCustomValidity($('#inputRecoveryCode').get(0), "Recovery code field is empty!");
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9]{8}$/;
            if (!regex.test(code))
            {
                  reportCustomValidity($('#inputRecoveryCode').get(0), "Recovery code format invalid!");
                  return;
            }
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/check_recovery_code.php',
            method: 'POST',
            data: { email: encodeData(globalEmail), code: code },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content_2').text(data.error);
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content_2').text('');
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'none';

                        $('#recovery_email_form').css('display', 'none');
                        $('#recovery_password_form').css('display', 'flex');
                        $('#recovery_code_form').css('display', 'none');
                  }
            },
            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  console.error(err);
                  if (err.status >= 500)
                  {
                        $('#error_message_content_2').text('Server encountered error!');
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content_2').text(err.responseJSON.error);
                        const error_message = document.getElementById('recovery_fail_2');
                        error_message.style.display = 'flex';
                  }
            }
      });
}

function changePassword(e, user_type)
{
      e.preventDefault();

      const password = encodeData($('#inputNewPassword').val());
      const confirmPassword = encodeData($('#inputConfirmNewPassword').val());

      if (globalEmail && globalEmail === '')
      {
            $('#error_message_content_2').text('No email provided!');
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'flex';
            return;
      }

      if (password === '')
      {
            reportCustomValidity($('#inputNewPassword').get(0), "New password field is empty!");
            return;
      }
      else if (password.length < 8)
      {
            reportCustomValidity($('#inputNewPassword').get(0), "New password must be at least 8 characters long!");
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,72}$/;
            if (!regex.test(password))
            {
                  reportCustomValidity($('#inputNewPassword').get(0), "New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters!");
                  return;
            }
      }

      if (confirmPassword === '')
      {
            reportCustomValidity($('#inputConfirmNewPassword').get(0), "Confirm new password field is empty!");
            return;
      }
      else if (confirmPassword != password)
      {
            reportCustomValidity($('#inputConfirmNewPassword').get(0), "Confirm new password does not match!");
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/change_password.php',
            method: 'POST',
            data: { email: encodeData(globalEmail), password: password, confirmPassword: confirmPassword, type: encodeData(user_type) },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content_3').text(data.error);
                        const error_message = document.getElementById('recovery_fail_3');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content_3').text('');
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
                  if (err.status >= 500)
                  {
                        $('#error_message_content_3').text('Server encountered error!');
                        const error_message = document.getElementById('recovery_fail_3');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content_3').text(err.responseJSON.error);
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
            $('#error_message_content_1').text('');
            const error_message = document.getElementById('recovery_fail_1');
            error_message.style.display = 'none';
            $('#recovery_email_form').css('display', 'flex');
      }

      {
            $('#error_message_content_3').text('');
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'none';
            $('#recovery_password_form').css('display', 'none');
      }

      {
            $('#error_message_content_2').text('');
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'none';
            $('#recovery_code_form').css('display', 'none');
      }

      $('input').val('');
}

function backToGetCode()
{
      {
            $('#error_message_content_1').text('');
            const error_message = document.getElementById('recovery_fail_1');
            error_message.style.display = 'none';
            $('#recovery_email_form').css('display', 'none');
      }

      {
            $('#error_message_content_3').text('');
            const error_message = document.getElementById('recovery_fail_3');
            error_message.style.display = 'none';
            $('#recovery_password_form').css('display', 'none');
            $('#recovery_password_form :input').val('');
      }

      {
            $('#error_message_content_2').text('');
            const error_message = document.getElementById('recovery_fail_2');
            error_message.style.display = 'none';
            $('#recovery_code_form').css('display', 'flex');
            $('#recovery_code_form :input').val('');
      }

      requestRecoveryCode();
}