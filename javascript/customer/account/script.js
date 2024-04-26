let mode = null, newImg = null;

$(document).ready(function ()
{
      document.getElementById('select-personal-info').addEventListener('click', function ()
      {
            if (mode !== 1)
            {
                  $('#personalInfoForm').css('display', 'flex');
                  $('#passwordForm').css('display', 'none');
                  $('#historyPurchase').css('display', 'none');
                  $('#otherTab').css('display', 'none');

                  $('p[name="select-tab"]').each(function ()
                  {
                        $(this).removeClass('selected');
                  });
                  $(this).addClass('selected');

                  mode = 1;
                  resetForm();
            }
      });

      document.getElementById('select-password').addEventListener('click', function ()
      {
            if (mode !== 2)
            {
                  $('#personalInfoForm').css('display', 'none');
                  $('#passwordForm').css('display', 'flex');
                  $('#historyPurchase').css('display', 'none');
                  $('#otherTab').css('display', 'none');

                  $('p[name="select-tab"]').each(function ()
                  {
                        $(this).removeClass('selected');
                  });
                  $(this).addClass('selected');

                  mode = 2;
                  resetForm();
                  $('#currentPasswordInput').focus();
            }
      });

      document.getElementById('select-purchases').addEventListener('click', function ()
      {
            if (mode !== 3)
            {
                  $('#historyPurchase').css('display', 'flex');
                  $('#personalInfoForm').css('display', 'none');
                  $('#passwordForm').css('display', 'none');
                  $('#otherTab').css('display', 'none');

                  $('p[name="select-tab"]').each(function ()
                  {
                        $(this).removeClass('selected');
                  });
                  $(this).addClass('selected');

                  mode = 3;
                  findOrder();
            }
      });

      document.getElementById('select-other').addEventListener('click', function ()
      {
            if (mode !== 4)
            {
                  $('#otherTab').css('display', 'flex');
                  $('#historyPurchase').css('display', 'none');
                  $('#personalInfoForm').css('display', 'none');
                  $('#passwordForm').css('display', 'none');

                  $('p[name="select-tab"]').each(function ()
                  {
                        $(this).removeClass('selected');
                  });
                  $(this).addClass('selected');

                  mode = 4;
            }
      });

      $('#passwordForm,#personalInfoForm').on('submit', function (e)
      {
            e.preventDefault();
            saveChange();
      });

      $('#search_order_form').on('submit', function (e)
      {
            e.preventDefault();
            findOrder();
      });

      mode = 1;

      resetForm();

      initToolTip();

      window.addEventListener('resize', trackScreenWidth);

      trackScreenWidth();

      $('#orderModal').on('hidden.bs.modal', function ()
      {
            $('#orderID').text('');
            $('#orderTime').text('');
            $('#orderPrice').text('');
            $('#orderDiscount').text('');
            $('#physicalDestination').text('');
      });
});

function trackScreenWidth()
{
      if (window.innerWidth < 768)
            $('#btn-grp').removeClass('btn-group').addClass('btn-group-vertical');
      else
            $('#btn-grp').addClass('btn-group').removeClass('btn-group-vertical');
}

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
            url: '/ajax_service/customer/account/update_personal_info.php',
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
            url: '/ajax_service/customer/account/update_password.php',
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

function deactivateAccount()
{
      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/customer/account/deactivate_account.php',
            method: 'PATCH',
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
                        window.location.href = '/';
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
      });
}

function deleteAccount()
{
      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/customer/account/delete_account.php',
            method: 'DELETE',
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
                        window.location.href = '/';
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
      });
}

function findOrder()
{
      const search = encodeData($('#search_order').val().replace('/-/g', ''));
      const date = encodeData($('#orderDateInput').val());

      $.ajax({
            url: '/ajax_service/customer/account/get_order_list.php',
            method: 'GET',
            data: { code: search, date: date },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#current_point').text(data.query_result[0]);
                        $('#table_body').empty();
                        for (let i = 0; i < data.query_result[1].length; i++)
                        {
                              let temp = `<td class='align-middle'>${ i + 1 }</td>
                                    <td class='align-middle'>${ data.query_result[1][i].orderCode }</td>
                                    <td class='align-middle'>${ data.query_result[1][i].purchaseTime }</td>
                                    <td class='align-middle'>$${ data.query_result[1][i].totalCost }</td>
                                    <td class='align-middle'>$${ data.query_result[1][i].totalDiscount }</td>
                                    <td class='align-middle col-4'>
                                    <div class='d-flex flex-column books pe-3'>`;

                              for (let j = 0; j < data.query_result[1][i].books.length; j++)
                                    temp += `<p class='my-2 text-nowrap'>${ data.query_result[1][i].books[j].name } - ${ data.query_result[1][i].books[j].edition } edition</p>`;

                              temp += '</div></td>'

                              $('#table_body').append(`
                              <tr>
                              ${ temp }
                              <td class='align-middle'><button name='open-order-modal' onclick='orderDetail(\"${ data.query_result[1][i].orderCode }\",\"${ data.query_result[1][i].purchaseTime }\",\"$${ data.query_result[1][i].totalCost }\",\"$${ data.query_result[1][i].totalDiscount }\")' class='btn btn-sm btn-info' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Order detail\"><i class=\"bi bi-info-circle text-white\"></i></button></td>
                              </tr>
                              `);
                        }

                        initToolTip();
                  }
            },

            error: function (err)
            {
                  
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
      });
}

async function orderDetail(code, time, price, discount)
{
      $('#orderID').text(code);
      $('#orderTime').text(time);
      $('#orderPrice').text(price);
      $('#orderDiscount').text(discount);

      let failed = false;

      await $.ajax({
            url: '/ajax_service/customer/account/file_order_detail.php',
            method: 'GET',
            data: { code: encodeData(code.replace('/-/g', '')) },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        if (data.query_result.length)
                        {
                              $('#fileCopyDisplay').css('display', 'flex');
                              $('#file_table_body').empty();
                              for (let i = 0; i < data.query_result.length; i++)
                              {
                                    let temp = '';
                                    temp += `<td class='align-middle'>${ i + 1 }</td>`
                                    temp += `<td class='align-middle'><a href="/book/book-detail?id=${ data.query_result[i].id }" alt="Go to book detail"><img src="${ data.query_result[i].imagePath }" alt=\"Book image\" class=\"book_image\"></img></a></td>`;
                                    temp += `<td class=\"col-2 align-middle\">${ data.query_result[i].name }</td>`;
                                    temp += `<td class=\"align-middle\">${ data.query_result[i].edition }</td>`;
                                    temp += `<td class=\"align-middle text-nowrap\">${ data.query_result[i].isbn }</td>`;

                                    {
                                          let div = `<div class='d-flex flex-column'>`;
                                          for (let j = 0; j < data.query_result[i].author.length; j++)
                                          {
                                                div += `<p class='my-2 text-nowrap'>${ data.query_result[i].author[j] }</p>`;
                                          }
                                          div += `</div>`;
                                          temp += `<td class='col-1 align-middle'>${ div }</td>`;
                                    }

                                    if (data.query_result[i].category.length)
                                    {
                                          let div = `<div class='d-flex flex-column'>`
                                          for (let j = 0; j < data.query_result[i].category.length; j++)
                                          {
                                                div += `<p class='my-2 text-nowrap'>${ data.query_result[i].category[j].name }&nbsp;<i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i].category[j].description ? data.query_result[i].category[j].description : 'N/A' }"></i></p>`;
                                          }
                                          div += `</div>`;
                                          temp += `<td class=\"align-middle col-1\">${ div }</td>`;
                                    }
                                    else temp += `<td class=\"align-middle col-1\">N/A</td>`;
                                    temp += `<td class=\"align-middle col-1\"><div class='d-flex flex-column'><p>${ data.query_result[i].publisher }</p><p class='text-nowrap'>${ data.query_result[i].publishDate }</p></div></td>`;
                                    temp += `<td class=\"align-middle col-1\"><div class='truncate'>${ data.query_result[i].description }</div></td>`;
                                    if (data.query_result[i].avgRating)
                                          temp += `<td class=\"align-middle col-1\"><span class='text-nowrap'><span class='text-warning'>${ displayRatingStars(data.query_result[i].avgRating) }</span>&nbsp;(${ data.query_result[i].avgRating })</span></td>`;
                                    else
                                          temp += `<td class='align-middle'></td>`;
                                    temp += `<td class=\"align-middle\">$${ data.query_result[i].price }</td>`;
                                    temp += `<td class='align-middle'><a target='_blank' alt='Read PDF File' title='Read PDF File' href="${ data.query_result[i].filePath }"><i class=\"bi bi-file-earmark-fill text-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Read PDF File\"></i></a></td>`;

                                    $('#file_table_body').append($('<tr>').append(temp));
                              }

                              initToolTip();
                        }
                        else
                        {
                              $('#fileCopyDisplay').css('display', 'none');
                        }
                  }
            },

            error: function (err)
            {
                  failed = true;

                  
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
      });

      if (failed) return;

      await $.ajax({
            url: '/ajax_service/customer/account/physical_order_detail.php',
            method: 'GET',
            data: { code: encodeData(code.replace('/-/g', '')) },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        if (data.query_result.length)
                        {
                              $('#physicalCopyDisplay').css('display', 'flex');
                              $('#physical_table_body').empty();
                              for (let i = 0; i < data.query_result.length; i++)
                              {
                                    let temp = '';
                                    temp += `<td class='align-middle'>${ i + 1 }</td>`
                                    temp += `<td class='align-middle'><a href="/book/book-detail?id=${ data.query_result[i].id }" alt="Go to book detail"><img src="${ data.query_result[i].imagePath }" alt=\"Book image\" class=\"book_image\"></img></a></td>`;
                                    temp += `<td class=\"col-2 align-middle\">${ data.query_result[i].name }</td>`;
                                    temp += `<td class=\"align-middle\">${ data.query_result[i].edition }</td>`;
                                    temp += `<td class=\"align-middle text-nowrap\">${ data.query_result[i].isbn }</td>`;

                                    {
                                          let div = `<div class='d-flex flex-column'>`;
                                          for (let j = 0; j < data.query_result[i].author.length; j++)
                                          {
                                                div += `<p class='my-2 text-nowrap'>${ data.query_result[i].author[j] }</p>`;
                                          }
                                          div += `</div>`;
                                          temp += `<td class='col-1 align-middle'>${ div }</td>`;
                                    }

                                    if (data.query_result[i].category.length)
                                    {
                                          let div = `<div class='d-flex flex-column'>`
                                          for (let j = 0; j < data.query_result[i].category.length; j++)
                                          {
                                                div += `<p class='my-2 text-nowrap'>${ data.query_result[i].category[j].name }&nbsp;<i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i].category[j].description ? data.query_result[i].category[j].description : 'N/A' }"></i></p>`;
                                          }
                                          div += `</div>`;
                                          temp += `<td class=\"align-middle col-1\">${ div }</td>`;
                                    }
                                    else temp += `<td class=\"align-middle col-1\">N/A</td>`;
                                    temp += `<td class=\"align-middle col-1\"><div class='d-flex flex-column'><p>${ data.query_result[i].publisher }</p><p class='text-nowrap'>${ data.query_result[i].publishDate }</p></div></td>`;
                                    temp += `<td class=\"align-middle col-1\"><div class='truncate'>${ data.query_result[i].description }</div></td>`;
                                    if (data.query_result[i].avgRating)
                                          temp += `<td class=\"align-middle col-1\"><span class='text-nowrap'><span class='text-warning'>${ displayRatingStars(data.query_result[i].avgRating) }</span>&nbsp;(${ data.query_result[i].avgRating })</span></td>`;
                                    else
                                          temp += `<td class='align-middle'></td>`;
                                    temp += `<td class=\"align-middle\">$${ data.query_result[i].price }</td>`;
                                    temp += `<td class=\"align-middle\">${ data.query_result[i].amount } ${ data.query_result[i].amount === 1 ? 'copy' : 'copies' }</td>`;
                                    $('#physicalDestination').text(data.query_result[i].destinationAddress);

                                    $('#physical_table_body').append($('<tr>').append(temp));
                              }

                              initToolTip();
                        }
                        else
                              $('#physicalCopyDisplay').css('display', 'none');
                  }
            },

            error: function (err)
            {
                  failed = true;

                  
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
      });

      if (failed) return;

      $('#orderModal').modal('show');

      $('div[role="tooltip"]').remove();
}