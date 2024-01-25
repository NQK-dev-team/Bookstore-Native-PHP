let update_id = null;

$(document).ready(function ()
{
      $('#updateCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmUpdateModal').modal('show');
      });

      $('#updateModal').on('hidden.bs.modal', function ()
      {
            update_id = null;
      });
});

function openUpdateModal(id)
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
            update_id = id;
      }
      else if (type === 2)
      {
            update_id = id;
      }
      else if (type === 3)
      {
            update_id = id;
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