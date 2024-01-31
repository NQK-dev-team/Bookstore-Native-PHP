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
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  fetchCustomerList();
            },
            error: function (err)
            {
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
      $('#deactivateModal').modal('hide');
}

function activateCustomer()
{
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
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  fetchCustomerList();
            },
            error: function (err)
            {
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
      $('#activateModal').modal('hide');
}