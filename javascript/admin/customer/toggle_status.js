let toggle_id = null;

$(document).ready(function ()
{
      $('#deactivateModal, #activateModal').on('hidden.bs.modal', function ()
      {
            toggle_id = null;
      });
});

function openDeactivateModal(id)
{
      toggle_id = id;
      $('#deactivateModal').modal('show');
}

function openActivateModal(id)
{
      toggle_id = id;
      $('#activateModal').modal('show');
}

function deactivateCustomer()
{
      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/customer/toggle_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(toggle_id),
                  status: false
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

function activateCustomer()
{
      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/customer/toggle_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(toggle_id),
                  status: true
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
                  }
                  else if (data.query_result === 100)
                        $('#deleteCancelNotifyModal').modal('show');
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