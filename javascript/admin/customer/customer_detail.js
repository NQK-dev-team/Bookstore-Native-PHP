let mode = null;

$(document).ready(function ()
{
      document.getElementById('btnradio1').addEventListener('change', function ()
      {
            if (this.checked)
            {
                  $('#personalInfoForm').css('display', 'flex');
                  $('#passwordForm').css('display', 'none');
                  $('#historyPurchase').css('display', 'none');
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
                  $('#historyPurchase').css('display', 'none');
                  mode = 2;
                  resetForm();
                  $('#newPasswordInput').focus();
            }
      });

      document.getElementById('btnradio3').addEventListener('change', function ()
      {
            if (this.checked)
            {
                  $('#historyPurchase').css('display', 'flex');
                  $('#personalInfoForm').css('display', 'none');
                  $('#passwordForm').css('display', 'none');
                  mode = 3;
                  findOrder();
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

function resetForm()
{
      if (mode === 1)
      {
            $('#userImage').attr('src', $('#userImage').data('initial-src'));
            $('#emailInput').val($('#emailInput').data('initial-value'));
            $('#phoneInput').val($('#phoneInput').data('initial-value'));
      }
      else if (mode === 2)
      {
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

function changeCustomerInfo()
{
      const email = encodeData(document.getElementById('emailInput').value);
      const phone = encodeData(document.getElementById('phoneInput').value);

      if (email === '')
      {
            reportCustomValidity($('#emailInput').get(0), "Email field is empty!");
            return;
      }
      else
      {
            const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            const localEmail = email.replace(/%40/g, '@');
            if (!regex.test(localEmail))
            {
                  reportCustomValidity($('#emailInput').get(0), "Email format invalid!");
                  return;
            }
            else if (email.length > 255)
            {
                  reportCustomValidity($('#emailInput').get(0), "Email must be 255 characters long or less!");
                  return;
            }
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

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/customer/update_info.php',
            method: 'PUT',
            data: { email, phone },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#nameInput').prop('disabled', true);
                  $('#dobInput').prop('disabled', true);
                  $('#genderInput').prop('disabled', true);
                  $('#addressInput').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#successModal').modal('show');
                        $('#emailInput').data('initial-value', $('#emailInput').val());
                        $('#phoneInput').data('initial-value', $('#phoneInput').val());
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#nameInput').prop('disabled', true);
                  $('#dobInput').prop('disabled', true);
                  $('#genderInput').prop('disabled', true);
                  $('#addressInput').prop('disabled', true);

                  $('#newPasswordInput').val('');
                  $('#confirmPasswordInput').val('');

                  
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
      const newPassword = encodeData($('#newPasswordInput').val());
      const confirmPassword = encodeData($('#confirmPasswordInput').val());

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
            url: '/ajax_service/admin/customer/update_password.php',
            method: 'PUT',
            data: { newPassword, confirmPassword },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#nameInput').prop('disabled', true);
                  $('#dobInput').prop('disabled', true);
                  $('#genderInput').prop('disabled', true);
                  $('#addressInput').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#successModal').modal('show');
                        $('#newPasswordInput').val('');
                        $('#confirmPasswordInput').val('');
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#nameInput').prop('disabled', true);
                  $('#dobInput').prop('disabled', true);
                  $('#genderInput').prop('disabled', true);
                  $('#addressInput').prop('disabled', true);

                  $('#newPasswordInput').val('');
                  $('#confirmPasswordInput').val('');

                  
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

function findOrder()
{
      const search = encodeData($('#search_order').val().replace('/-/g', ''));
      const date = encodeData($('#orderDateInput').val());

      $.ajax({
            url: '/ajax_service/admin/customer/get_order_list.php',
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
                        $('#current_point').text(data.query_result[0].point);
                        $('#current_ref').text(data.query_result[0].refNumber);
                        $('#loyalty_discount').text(data.query_result[0].loyaltyDiscount ? (data.query_result[0].loyaltyDiscount + '%') : data.query_result[0].loyaltyDiscount);
                        $('#ref_discount').text(data.query_result[0].refDiscount ? (data.query_result[0].refDiscount + '%') : data.query_result[0].refDiscount);
                        
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
                              <td class='align-middle'><button onclick='orderDetail(\"${ data.query_result[1][i].orderCode }\",\"${ data.query_result[1][i].purchaseTime }\",\"$${ data.query_result[1][i].totalCost }\",\"$${ data.query_result[1][i].totalDiscount }\")' class='btn btn-sm btn-info' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Order detail\"><i class=\"bi bi-info-circle text-white\"></i></button></td>
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
            url: '/ajax_service/admin/customer/file_order_detail.php',
            method: 'GET',
            data: { code: code.replace('/-/g', '') },
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
                                    temp += `<td class='align-middle'><a href="/admin/book/edit-book?id=${ data.query_result[i].id}" alt="Go to book detail"><img src="${ data.query_result[i].imagePath }" alt=\"Book image\" class=\"book_image\"></img></a></td>`;
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
            url: '/ajax_service/admin/customer/physical_order_detail.php',
            method: 'GET',
            data: { code: code.replace('/-/g', '') },
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
                                    temp += `<td class='align-middle'><a href="/admin/book/edit-book?id=${ data.query_result[i].id }" alt="Go to book detail"><img src="${ data.query_result[i].imagePath }" alt=\"Book image\" class=\"book_image\"></img></a></td>`;
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