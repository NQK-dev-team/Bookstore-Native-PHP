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

function deleteCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/coupon/delete_discount.php',
            type: 'DELETE',
            data: {
                  id: encodeData(delete_id),
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
                        $('#deleteModal').modal('hide');
                  }
                  fetchCouponList();
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
      $('#deleteModal').modal('hide');

}