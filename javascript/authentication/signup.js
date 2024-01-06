function signUpHandler(event)
{
      event.preventDefault();
      const name = sanitize(document.getElementById('inputName').value);
      const date = sanitize(document.getElementById('inputDate').value);
      const phone = sanitize(document.getElementById('inputPhone').value);
      const address = sanitize(document.getElementById('inputAddress').value);
      const email = (sanitize(document.getElementById('inputEmail').value)).replace(/%40/g, '@');
      const password = sanitize(document.getElementById('inputPassword').value);

      let isOK = true;

      if (name === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Name field is empty!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }

      if (date === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Date of birth field is empty!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }
      else if (!isAgeValid(date) && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'You must be at least 18 years old!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }

      if (phone === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Phone number field is empty!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[0-9]{10}$/;
            if (!regex.test(phone) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content');
                  p_elem.innerHTML = 'Phone number format invalid!';
                  const error_message = document.getElementById('signup_fail');
                  error_message.style.display = 'flex';
            }
      }

      if (email === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Email field is empty!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            if (!regex.test(email) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content');
                  p_elem.innerHTML = 'Email format invalid!';
                  const error_message = document.getElementById('signup_fail');
                  error_message.style.display = 'flex';
            }
      }

      if (password === '' && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Password field is empty!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }
      else if (password.length < 8 && isOK)
      {
            isOK = false;
            const p_elem = document.getElementById('error_message_content');
            p_elem.innerHTML = 'Password must be at least 8 characters!';
            const error_message = document.getElementById('signup_fail');
            error_message.style.display = 'flex';
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/;
            if (!regex.test(password) && isOK)
            {
                  isOK = false;
                  const p_elem = document.getElementById('error_message_content');
                  p_elem.innerHTML = 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character!';
                  const error_message = document.getElementById('signup_fail');
                  error_message.style.display = 'flex';
            }
      }

      if (isOK)
            $.ajax({
                  url: '/ajax_service/authentication/signup_handler.php',
                  method: 'POST',
                  data: { name: name, date: date, phone: phone, address: (address === '' || !address) ? null : address, email: email, password: password },
                  //dataType: 'json',
                  success: function (data)
                  {
                        console.log(data);
                        if (data.error)
                        {
                              const p_elem = document.getElementById('error_message_content');
                              p_elem.innerHTML = data.error;
                              const error_message = document.getElementById('signup_fail');
                              error_message.style.display = 'flex';
                        }
                        // else if (data.query_result)
                        // {
                        //       if (user_type === 'customer')
                        //             window.location.href = '/';
                        //       if (user_type === 'admin')
                        //             window.location.href = '/admin/';
                        // }
                  },
                  error: function (err)
                  {
                        console.error(err);
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

function checkEmailUsed()
{
      const email = (sanitize(document.getElementById('inputEmail').value)).replace(/%40/g, '@');

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

function checkAge()
{
      const dobInput = document.getElementById("inputDate").value;

      const elem = document.getElementById('invalid_age');

      if (dobInput === '')
            elem.style.display = 'none';
      else
      {
            if (isAgeValid(dobInput))
                  elem.style.display = 'none';
            else
                  elem.style.display = 'flex';
      }
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