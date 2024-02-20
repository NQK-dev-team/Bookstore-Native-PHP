let delete_id = null;

$(document).ready(function ()
{
      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            delete_id = null;
      });
});

function openDeleteModal(id)
{
      delete_id = id;
      $('#deleteModal').modal('show');
}

function deleteCustomer()
{
      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/customer/delete_customer.php',
            type: 'DELETE',
            data: {
                  id: encodeData(delete_id)
            },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#next_button').prop('disabled', nextBtnDisabledProp);
                  $('#prev_button').prop('disabled', prevBtnDisabledProp);
                  $('#list_offset').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  } else if (data.query_result)
                  {
                        if (data.query_result === 1)
                              $('#deleteNotifyModal1').modal('show');
                        else if (data.query_result === 2)
                              $('#deleteNotifyModal2').modal('show');
                  }
                  fetchCustomerList();
            },
            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#next_button').prop('disabled', nextBtnDisabledProp);
                  $('#prev_button').prop('disabled', prevBtnDisabledProp);
                  $('#list_offset').prop('disabled', true);

                  console.error(err);

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