$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#signUpSuccessModal').on('hidden.bs.modal', function ()
      {
            window.location.href = "/authentication/";
      });

      initToolTip();

      $('#inputName').focus();
});

function signUpHandler(event)
{
      event.preventDefault();

      const name = encodeData(document.getElementById('inputName').value);
      const date = encodeData(document.getElementById('inputDate').value);
      const phone = encodeData(document.getElementById('inputPhone').value);
      const address = encodeData(document.getElementById('inputAddress').value);
      const email = encodeData(document.getElementById('inputEmail').value);
      const password = encodeData(document.getElementById('inputPassword').value);
      const refEmail = encodeData(document.getElementById('inputRefEmail').value);
      const gender = encodeData(document.getElementById('inputGender').value);
      const confirmPassword = encodeData(document.getElementById('confirmPassword').value);

      if (name === '')
      {
            reportCustomValidity($('#inputName').get(0), "Name field is empty!");
            return;
      } else if (name.length > 255)
      {
            reportCustomValidity($('#inputName').get(0), "Name must be 255 characters long or less!");
            return;
      }

      if (date === '')
      {
            reportCustomValidity($('#inputDate').get(0), "Date of birth field is empty!");
            return;
      }
      else if (!isDobValid(date))
      {
            reportCustomValidity($('#inputDate').get(0), "Date of birth invalid!");
            return;
      }
      else if (!isAgeValid(date))
      {
            reportCustomValidity($('#inputDate').get(0), "You must be at least 18 years old to sign up!");
            return;
      }

      if (gender === 'null')
      {
            reportCustomValidity($('#inputGender').get(0), "Gender field is empty!");
            return;
      }
      else if (gender !== 'F' && gender !== 'M' && gender !== 'O')
      {
            reportCustomValidity($('#inputGender').get(0), "Invalid gender!");
            return;
      }

      if (phone === '')
      {
            reportCustomValidity($('#inputPhone').get(0), "Phone number field is empty!");
            return;
      }
      else
      {
            const regex = /^[0-9]{10}$/;
            if (!regex.test(phone))
            {
                  reportCustomValidity($('#inputPhone').get(0), "Phone number format invalid!");
                  return;
            }
      }

      if (email === '')
      {
            reportCustomValidity($('#inputEmail').get(0), "Email field is empty!");
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            const localEmail = email.replace(/%40/g, '@');
            if (!regex.test(localEmail))
            {
                  reportCustomValidity($('#inputEmail').get(0), "Email format invalid!");
                  return;
            }
            else if (email.length > 255)
            {
                  reportCustomValidity($('#inputEmail').get(0), "Email must be 255 characters long or less!");
                  return;
            }
      }

      if (password === '')
      {
            reportCustomValidity($('#inputPassword').get(0), "Password field is empty!");
            return;
      }
      else if (password.length < 8)
      {
            reportCustomValidity($('#inputPassword').get(0), "Password must be at least 8 characters long!");
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,72}$/;
            if (!regex.test(password))
            {
                  reportCustomValidity($('#inputPassword').get(0), "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters!");
                  return;
            }
      }

      if (confirmPassword === '')
      {
            reportCustomValidity($('#confirmPassword').get(0), "Confirm password field is empty!");
            return;
      }
      else if (confirmPassword !== password)
      {
            reportCustomValidity($('#confirmPassword').get(0), "Confirm password does not match!");
            return;
      }

      if (refEmail !== '')
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            const localEmail = refEmail.replace(/%40/g, '@');
            if (!regex.test(localEmail))
            {
                  reportCustomValidity($('#inputRefEmail').get(0), "Referrer email format invalid!");
                  return;
            }
            else if (refEmail.length > 255)
            {
                  reportCustomValidity($('#inputRefEmail').get(0), "Refferer email must be 255 characters long or less!");
                  return;
            }
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/signup_handler.php',
            method: 'POST',
            data: { gender: gender, name: name, date: date, phone: phone, address: (address === '' || !address) ? null : address, email: email, password: password, confirmPassword: confirmPassword, refEmail: (refEmail === '' || !refEmail) ? null : refEmail },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#error_message_content').text(data.error);
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        $('#error_message_content').text('');
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'none';

                        $('#signUpSuccessModal').modal('show');
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
                        $('#error_message_content').text('Server encountered error!');
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content').text(err.responseJSON.error);
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  }
            }
      });
}

function checkPhoneUsed()
{
      const phone = encodeData(document.getElementById('inputPhone').value);

      $.ajax({
            url: `/ajax_service/authentication/check_phone.php`,
            method: 'POST',
            data: { phone: phone },
            dataType: 'json',
            success: function (data)
            {
                  $('#error_message_content').text('');
                  const error_message = document.getElementById('signup_fail');
                  error_message.style.display = 'none';

                  const elem = document.getElementById('phone_used_error');
                  if (data.query_result)
                        elem.style.display = 'flex';
                  else
                        elem.style.display = 'none';
            },
            error: function (err)
            {
                  console.error(err);
                  if (err.status >= 500)
                  {
                        $('#error_message_content').text('Server encountered error!');
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  } else
                  {
                        $('#error_message_content').text(err.responseJSON.error);
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  }
            }
      });
}

function checkEmailUsed(isRefEmail)
{
      if (!isRefEmail)
      {
            const email = encodeData(document.getElementById('inputEmail').value);

            $.ajax({
                  url: `/ajax_service/authentication/check_email.php`,
                  method: 'POST',
                  data: { email: email },
                  dataType: 'json',
                  success: function (data)
                  {
                        $('#error_message_content').text('');
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'none';

                        const elem = document.getElementById('email_used_error');
                        if (data.query_result)
                              elem.style.display = 'flex';
                        else
                              elem.style.display = 'none';
                  },
                  error: function (err)
                  {
                        console.error(err);
                        if (err.status >= 500)
                        {
                              $('#error_message_content').text('Server encountered error!');
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        } else
                        {
                              $('#error_message_content').text(err.responseJSON.error);
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
      else
      {
            const email = encodeData(document.getElementById('inputRefEmail').value);

            if (email === '')
            {
                  const elem = document.getElementById('ref_email_error');
                  elem.style.display = 'none';
                  return;
            }

            $.ajax({
                  url: `/ajax_service/authentication/check_email.php`,
                  method: 'POST',
                  data: { email: email },
                  dataType: 'json',
                  success: function (data)
                  {
                        $('#error_message_content').text('');
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'none';

                        const elem = document.getElementById('ref_email_error');
                        if (data.query_result)
                              elem.style.display = 'none';
                        else
                              elem.style.display = 'flex';
                  },
                  error: function (err)
                  {
                        console.error(err);
                        if (err.status >= 500)
                        {
                              $('#error_message_content').text('Server encountered error!');
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        } else
                        {
                              $('#error_message_content').text(err.responseJSON.error);
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}