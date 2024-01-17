$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#signUpSuccessModal').on('hidden.bs.modal', function ()
      {
            window.location.href = "/authentication/";
      });

      initToolTip();
});

function signUpHandler(event)
{
      event.preventDefault();

      const name = sanitize(document.getElementById('inputName').value);
      const date = sanitize(document.getElementById('inputDate').value);
      const phone = sanitize(document.getElementById('inputPhone').value);
      const address = sanitize(document.getElementById('inputAddress').value);
      const email = sanitize(document.getElementById('inputEmail').value);
      const password = sanitize(document.getElementById('inputPassword').value);
      const card = sanitize(document.getElementById('inputCard').value);
      const refEmail = sanitize(document.getElementById('inputRefEmail').value);

      if (name === '')
      {
            reportCustomValidity($('#inputName').get(0), "Name field is empty!");
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

      if (card !== '')
      {
            const regex = /^[0-9]{8,16}$/;
            if (!regex.test(card))
            {
                  reportCustomValidity($('#inputCard').get(0), "Card number format invalid!");
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
            if (!regex.test(email))
            {
                  reportCustomValidity($('#inputEmail').get(0), "Email format invalid!");
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
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password))
            {
                  reportCustomValidity($('#inputPassword').get(0), "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!");
                  return;
            }
      }

      if (refEmail !== '')
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(refEmail))
            {
                  reportCustomValidity($('#inputRefEmail').get(0), "Referrer email format invalid!");
                  return;
            }
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/authentication/signup_handler.php',
            method: 'POST',
            data: { name: name, date: date, phone: phone, address: (address === '' || !address) ? null : address, card: (card === '' || !card) ? null : card, email: email, password: password, refEmail: (refEmail === '' || !refEmail) ? null : refEmail },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = data.error;
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  }
                  else if (data.query_result)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = '';
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
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = 'Server encountered error!';
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  } else
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = err.responseJSON.error;
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  }
            }
      });
}

function checkPhoneUsed()
{
      const phone = sanitize(document.getElementById('inputPhone').value);

      $.ajax({
            url: `/ajax_service/authentication/check_phone.php`,
            method: 'POST',
            data: { phone: phone },
            dataType: 'json',
            success: function (data)
            {
                  const p_elem = document.getElementById('error_message_content');
                  p_elem.innerHTML = '';
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
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = 'Server encountered error!';
                        const error_message = document.getElementById('signup_fail');
                        error_message.style.display = 'flex';
                  } else
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = err.responseJSON.error;
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
            const email = sanitize(document.getElementById('inputEmail').value);

            $.ajax({
                  url: `/ajax_service/authentication/check_email.php`,
                  method: 'POST',
                  data: { email: email },
                  dataType: 'json',
                  success: function (data)
                  {
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = '';
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
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        } else
                        {
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = err.responseJSON.error;
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
      else
      {
            const email = sanitize(document.getElementById('inputRefEmail').value);

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
                        const p_elem = document.getElementById('error_message_content');
                        p_elem.innerHTML = '';
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
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        } else
                        {
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = err.responseJSON.error;
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}