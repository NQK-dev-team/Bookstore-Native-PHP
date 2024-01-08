$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#signUpSuccessModal').on('hidden.bs.modal', function ()
      {
            window.location.href = "/authentication/";
      });
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

      let isOK = true;

      if (name === '' && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputName');
            elem.setCustomValidity("Name field is empty!");
            elem.reportValidity();
      }

      if (date === '' && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputDate');
            elem.setCustomValidity("Date of birth field is empty!");
            elem.reportValidity();
      }
      else if (!isDobValid(date) && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputDate');
            elem.setCustomValidity("Date of birth invalid!");
            elem.reportValidity();
      }
      else if (!isAgeValid(date) && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputDate');
            elem.setCustomValidity("You must be at least 18 years old to sign up!");
            elem.reportValidity();
      }

      if (phone === '' && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputPhone');
            elem.setCustomValidity("Phone number field is empty!");
            elem.reportValidity();
      }
      else
      {
            const regex = /^[0-9]{10}$/;
            if (!regex.test(phone) && isOK)
            {
                  isOK = false;
                  const elem = document.getElementById('inputPhone');
                  elem.setCustomValidity("Phone number format invalid!");
                  elem.reportValidity();
            }
      }

      if (card !== '' && isOK)
      {
            const regex = /^[0-9]{8,16}$/;
            if (!regex.test(card) && isOK)
            {
                  isOK = false;
                  const elem = document.getElementById('inputCard');
                  elem.setCustomValidity("Card number format invalid!");
                  elem.reportValidity();
            }
      }

      if (email === '' && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputEmail');
            elem.setCustomValidity("Email field is empty!");
            elem.reportValidity();
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(email) && isOK)
            {
                  isOK = false;
                  const elem = document.getElementById('inputEmail');
                  elem.setCustomValidity("Email format invalid!");
                  elem.reportValidity();
            }
      }

      if (password === '' && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputPassword');
            elem.setCustomValidity("Password field is empty!");
            elem.reportValidity();
      }
      else if (password.length < 8 && isOK)
      {
            isOK = false;
            const elem = document.getElementById('inputPassword');
            elem.setCustomValidity("Password must be at least 8 characters!");
            elem.reportValidity();
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password) && isOK)
            {
                  isOK = false;
                  const elem = document.getElementById('inputPassword');
                  elem.setCustomValidity("Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!");
                  elem.reportValidity();
            }
      }

      if (refEmail !== '' && isOK)
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(refEmail) && isOK)
            {
                  isOK = false;
                  const elem = document.getElementById('inputRefEmail');
                  elem.setCustomValidity("Referrer email format invalid!");
                  elem.reportValidity();
            }
      }

      if (isOK)
      {
            $('*').addClass('wait');
            $.ajax({
                  url: '/ajax_service/authentication/signup_handler.php',
                  method: 'POST',
                  data: { name: name, date: date, phone: phone, address: (address === '' || !address) ? null : address, card: (card === '' || !card) ? null : card, email: email, password: password, refEmail: (refEmail === '' || !refEmail) ? null : refEmail },
                  dataType: 'json',
                  success: function (data)
                  {
                        $('*').removeClass('wait');
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
                        if (err.status === 500)
                        {
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = 'Server encountered error!';
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        } else if (err.status === 400)
                        {
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = 'Server request error!';
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                  }
            });
      }
}

function checkPhoneUsed()
{
      const phone = sanitize(document.getElementById('inputPhone').value);

      const data = { phone: phone };

      const queryString = $.param(data);

      $.ajax({
            url: `/ajax_service/authentication/check_phone.php?${ queryString }`,
            method: 'GET',
            dataType: 'json',
            success: function (data)
            {
                  const elem = document.getElementById('phone_used_error');
                  if (data.query_result)
                        elem.style.display = 'flex';
                  else
                        elem.style.display = 'none';
            },
            error: function (err)
            {
                  console.error(err);
            }
      });
}

function checkEmailUsed(isRefEmail)
{
      if (!isRefEmail)
      {
            const email = sanitize(document.getElementById('inputEmail').value);

            const data = { email: email };

            const queryString = $.param(data);

            $.ajax({
                  url: `/ajax_service/authentication/check_email.php?${ queryString }`,
                  method: 'GET',
                  dataType: 'json',
                  success: function (data)
                  {
                        const elem = document.getElementById('email_used_error');
                        if (data.query_result)
                              elem.style.display = 'flex';
                        else
                              elem.style.display = 'none';
                  },
                  error: function (err)
                  {
                        console.error(err);
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

            const data = { email: email };

            const queryString = $.param(data);

            $.ajax({
                  url: `/ajax_service/authentication/check_email.php?${ queryString }`,
                  method: 'GET',
                  dataType: 'json',
                  success: function (data)
                  {
                        const elem = document.getElementById('ref_email_error');
                        if (data.query_result)
                              elem.style.display = 'none';
                        else
                              elem.style.display = 'flex';
                  },
                  error: function (err)
                  {
                        console.error(err);
                  }
            });
      }
}

function checkAge()
{
      const dobInput = sanitize(document.getElementById("inputDate").value);

      const elem1 = document.getElementById('invalid_dob');
      const elem2 = document.getElementById('invalid_age');

      if (dobInput === '')
      {
            elem1.style.display = 'none';
            elem2.style.display = 'none';
            return;
      }

      if (isDobValid(dobInput))
      {
            elem1.style.display = 'none';

            if (isAgeValid(dobInput))
                  elem2.style.display = 'none';
            else
                  elem2.style.display = 'flex';
      }
      else
            elem1.style.display = 'flex';
}

function isDobValid(input)
{
      const dob = new Date(input);
      const today = new Date();

      return today >= dob;
}

function isAgeValid(input)
{
      const dob = new Date(input);
      const today = new Date();
      const age = today.getFullYear() - dob.getFullYear();

      // Check if the birthday has occurred this year
      if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate()))
            age--;

      return age >= 18;
}