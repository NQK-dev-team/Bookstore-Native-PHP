let mode = null, newImg = null;

$(document).ready(function ()
{
      document.getElementById('btnradio1').addEventListener('change', function ()
      {
            if (this.checked)
            {
                  $('#personalInfoForm').css('display', 'flex');
                  $('#passwordForm').css('display', 'none');
                  mode = 1;
                  resetForm();
            }
      });

      document.getElementById('btnradio2').addEventListener('change', function ()
      {
            if (this.checked)
            {
                  $('#personalInfoForm').css('display', 'none');
                  $('#passwordForm').css('display', 'flex');
                  mode = 2;
                  resetForm();
                  $('#currentPasswordInput').focus();
            }
      });

      $('#passwordForm,#personalInfoForm').on('submit', function (e)
      {
            e.preventDefault();
            saveChange();
      });

      mode = 1;

      resetForm();

      initToolTip();
});

function setNewImage(e)
{
      const file = e.target.files;
      $('#imageFileName').text(file.length === 1 ? file[0].name : '');
      newImg = file.length === 1 ? file[0] : null;

      if (file.length === 1)
      {
            const reader = new FileReader();

            reader.onload = function (e)
            {
                  $('#userImage').attr('src', e.target.result);
            };

            reader.readAsDataURL(file[0]);
      }
      else
            $('#userImage').attr('src', originalImg);
}

function resetForm()
{
      if (mode === 1)
      {
            $('#nameInput').val($('#nameInput').data('initial-value'));
            $('#emailInput').val($('#emailInput').data('initial-value'));
            $('#phoneInput').val($('#phoneInput').data('initial-value'));
            $('#addressInput').val($('#addressInput').data('initial-value'));
            $('#dobInput').val($('#dobInput').data('initial-value'));
            $('#genderInput').val($('#genderInput').data('initial-value'));
            $('#userImage').prop('src', $('#userImage').data('initial-src'));
            $('#imageFileName').text('');
            $('#imageInput').val('');
            newImg = null;
      }
      else if (mode === 2)
      {
            $('#currentPasswordInput').val('');
            $('#newPasswordInput').val('');
            $('#confirmPasswordInput').val('');
      }
}

function saveChange()
{
      clearAllCustomValidity();
      if (mode === 1)
            $('#confirmPersonalModal').modal('show');
      else if (mode === 2)
            $('#confirmPasswordModal').modal('show');
}

function changePersonalInfo()
{
      const name = encodeData($('#nameInput').val());
      const phone = encodeData($('#phoneInput').val());
      const address = encodeData($('#addressInput').val());
      const dob = encodeData($('#dobInput').val());
      const gender = encodeData($('#genderInput').val());

      if (newImg && newImg.type !== 'image/jpeg' && newImg.type !== 'image/png')
      {
            $('#imgeFileErrorMessage').text('Invalid image file!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            return;
      }
      else if (newImg && newImg.size > 5 * 1024 * 1024)
      {
            $('#imgeFileErrorMessage').text('Image size must be 5MB or less!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            return;
      }
      else
      {
            $('#imgeFileErrorMessage').text('');
            $('#imgeFileError').addClass('d-none').removeClass('d-flex');
      }

      if (name === '')
      {
            reportCustomValidity($('#nameInput').get(0), "Name field is empty!");
            return;
      }
      else if (name.length > 255)
      {
            reportCustomValidity($('#nameInput').get(0), "Name must be 255 characters long or less!");
            return;
      }

      if (phone === '')
      {
            reportCustomValidity($('#phoneInput').get(0), "Phone number field is empty!");
            return;
      }
      else
      {
            const regex = /^[0-9]{10}$/;
            if (!regex.test(phone))
            {
                  reportCustomValidity($('#phoneInput').get(0), "Phone number format invalid!");
                  return;
            }
      }

      if (address === '')
      {
            reportCustomValidity($('#addressInput').get(0), "Address field is empty!");
            return;
      }
      else if (address.length > 1000)
      {
            reportCustomValidity($('#addressInput').get(0), "Address must be 1000 characters long or less!");
            return;
      }

      if (dob === '')
      {
            reportCustomValidity($('#dobInput').get(0), "Date of birth field is empty!");
            return;
      }
      else if (!isDobValid(dob))
      {
            reportCustomValidity($('#dobInput').get(0), "Date of birth invalid!");
            return;
      }
      else if (!isAgeValid(dob))
      {
            reportCustomValidity($('#dobInput').get(0), "You must be at least 18 years old to sign up!");
            return;
      }

      if (gender === 'null')
      {
            reportCustomValidity($('#genderInput').get(0), "Gender field is empty!");
            return;
      }
      else if (gender !== 'F' && gender !== 'M' && gender !== 'O')
      {
            reportCustomValidity($('#genderInput').get(0), "Invalid gender!");
            return;
      }

      const postData = new FormData();
      postData.append('name', name);
      postData.append('phone', phone);
      postData.append('address', address);
      postData.append('dob', dob);
      postData.append('gender', gender);
      postData.append('image', newImg);

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/account/update_personal_info.php',
            method: 'POST',
            data: postData,
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#emailInput').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#successModal').modal('show');

                        $('#nameInput').data('initial-value', $('#nameInput').val());
                        $('#phoneInput').data('initial-value', $('#phoneInput').val());
                        $('#addressInput').data('initial-value', $('#addressInput').val());
                        $('#dobInput').data('initial-value', $('#dobInput').val());
                        $('#genderInput').data('initial-value', $('#genderInput').val());
                        $('#userImage').data('initial-src', $('#userImage').prop('src'));
                        $('#imageFileName').text('');
                        $('#imageInput').val('');
                        newImg = null;
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#emailInput').prop('disabled', true);


                  if (err.status >= 500)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text('Server encountered error!');
                  } else
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(err.responseJSON.error);
                  }
            }
      })
}

function changePassword()
{
      const oldPassword = encodeData($('#currentPasswordInput').val());
      const newPassword = encodeData($('#newPasswordInput').val());
      const confirmPassword = encodeData($('#confirmPasswordInput').val());

      if (oldPassword === '')
      {
            reportCustomValidity($('#currentPasswordInput').get(0), "Current password field is empty!");
            return;
      }

      if (newPassword === '')
      {
            reportCustomValidity($('#newPasswordInput').get(0), "New password field is empty!");
            return;
      }
      else if (newPassword.length < 8)
      {
            reportCustomValidity($('#newPasswordInput').get(0), "New password must be at least 8 characters long!");
            return;
      }
      else
      {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,72}$/;
            if (!regex.test(newPassword))
            {
                  reportCustomValidity($('#newPasswordInput').get(0), "New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters!");
                  return;
            }
      }

      if (confirmPassword === '')
      {
            reportCustomValidity($('#confirmPasswordInput').get(0), "Confirm new password field is empty!");
            return;
      }
      else if (confirmPassword !== newPassword)
      {
            reportCustomValidity($('#confirmPasswordInput').get(0), "Confirm new password does not match!");
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/account/update_password.php',
            method: 'PUT',
            data: { oldPassword, newPassword, confirmPassword },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#emailInput').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#successModal').modal('show');
                        $('#currentPasswordInput').val('');
                        $('#newPasswordInput').val('');
                        $('#confirmPasswordInput').val('');
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#emailInput').prop('disabled', true);


                  if (err.status >= 500)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text('Server encountered error!');
                  } else
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(err.responseJSON.error);
                  }
            }
      })
}