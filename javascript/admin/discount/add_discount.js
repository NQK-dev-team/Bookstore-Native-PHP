$(document).ready(function ()
{
      $('#addCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmAddModal').modal('show');
      });

      $('#addModal').on('hidden.bs.modal', function ()
      {
            $('#addCouponForm').empty();
      });
});

function openAddModal()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponStartDate" class="form-label">Start Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="date" class="form-control" id="couponStartDate">
                  </div>
                  <div class='mt-2'>
                        <label for="couponEndDate" class="form-label">End Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="date" class="form-control" id="couponEndDate">
                  </div>
                  <div class='mt-2'>
                        <label for="couponBookApply" class="form-label">Books Applied:</label>
                        <input readonly type="text" class="form-control pointer" id="couponBookApply" onclick="chooseBook()">
                  </div>`)
            );
      else if (type === 2)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                              <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPoint" class="form-label">Accumulated Points:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPoint" placeholder="Enter value">
                  </div>`)
            );
      else if (type === 3)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                              <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPeople" class="form-label">Number of People:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPeople" placeholder="Enter value">
                  </div>`)
            );


      $('#addModal').modal('show');
}

function addCoupon()
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
            const name = encodeData($('#couponName').val());
            const discount = $('#couponPercentage').val() ? parseFloat(encodeData($('#couponPercentage').val())) : '';

            if (!name)
            {
                  reportCustomValidity($('#couponName').get(0), 'Missing coupon name!');
                  return;
            }

            if (discount === '')
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Missing discount percentage!');
                  return;
            }
            if (typeof discount !== 'number' || isNaN(discount) || discount <= 0 || discount > 100)
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Discount percentage invalid!');
                  return;
            }
      }
      else if (type === 2)
      {
            const name = encodeData($('#couponName').val());
            const discount = $('#couponPercentage').val() ? parseFloat(encodeData($('#couponPercentage').val())) : '';
            const point = $('#couponPoint').val() ? parseFloat(encodeData($('#couponPoint').val())) : '';

            if (!name)
            {
                  reportCustomValidity($('#couponName').get(0), 'Missing coupon name!');
                  return;
            }

            if (discount === '')
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Missing discount percentage value!');
                  return;
            }
            else if (typeof discount !== 'number' || isNaN(discount) || discount <= 0 || discount > 100)
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Discount percentage value invalid!');
                  return;
            }

            if (point === '')
            {
                  reportCustomValidity($('#couponPoint').get(0), 'Missing accumulated point value!');
                  return;
            }
            else if (typeof point !== 'number' || isNaN(point) || point <= 0)
            {
                  reportCustomValidity($('#couponPoint').get(0), 'Accumulated point value invalid!');
                  return;
            }

            $.ajax({
                  url: '/ajax_service/admin/discount/add_discount.php',
                  type: 'POST',
                  data: {
                        type: type,
                        name: name,
                        discount: discount,
                        point: point
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
                              $('#addModal').modal('hide');
                              $('#successAddModal').modal('show');
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
      }
      else if (type === 3)
      {
            const name = encodeData($('#couponName').val());
            const discount = $('#couponPercentage').val() ? parseFloat(encodeData($('#couponPercentage').val())) : '';
            const people = $('#couponPeople').val() ? parseInt(encodeData($('#couponPeople').val())) : '';

            if (!name)
            {
                  reportCustomValidity($('#couponName').get(0), 'Missing coupon name!');
                  return;
            }

            if (discount === '')
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Missing discount percentage value!');
                  return;
            }
            else if (typeof discount !== 'number' || isNaN(discount) || discount <= 0 || discount > 100)
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Discount percentage value invalid!');
                  return;
            }

            if (people === '')
            {
                  reportCustomValidity($('#couponPeople').get(0), 'Missing number of people value!');
                  return;
            }
            else if (typeof people !== 'number' || isNaN(people) || people <= 0)
            {
                  reportCustomValidity($('#couponPeople').get(0), 'Number of people value invalid!');
                  return;
            }

            $.ajax({
                  url: '/ajax_service/admin/discount/add_discount.php',
                  type: 'POST',
                  data: {
                        type: type,
                        name: name,
                        discount: discount,
                        people: people
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
                              $('#addModal').modal('hide');
                              $('#successAddModal').modal('show');
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
      }
}