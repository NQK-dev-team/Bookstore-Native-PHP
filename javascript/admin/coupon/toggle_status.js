let activate_id = null, deactivate_id = null;

$(document).ready(function ()
{
      $('#deactivateModal').on('hidden.bs.modal', function ()
      {
            deactivate_id = null;
      });

      $('#activateModal').on('hidden.bs.modal', function ()
      {
            activate_id = null;
      });
});

function openActivateModal(id)
{
      activate_id = id;
      $('#activateModal').modal('show');
}

function activateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/coupon/update_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(activate_id),
                  status: true,
                  type: encodeData(type)
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
                  else if (data.query_result)
                  {
                        $('#activateModal').modal('hide');
                  }
                  fetchCouponList();
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
      $('#activateModal').modal('hide');
}

function openDeactivateModal(id)
{
      deactivate_id = id;
      $('#deactivateModal').modal('show');
}

function deactivateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/coupon/update_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(deactivate_id),
                  status: false,
                  type: encodeData(type)
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
                  else if (data.query_result)
                  {
                        $('#deactivateModal').modal('hide');
                  }
                  fetchCouponList();
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
      $('#deactivateModal').modal('hide');
}