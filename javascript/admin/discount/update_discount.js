let update_id = null;

$(document).ready(function ()
{
      $('#updateCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmUpdateModal').modal('show');
      });
});

function openUpdateModal()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
      {

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}

function updateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
      {

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}

function resetForm()
{
      
}