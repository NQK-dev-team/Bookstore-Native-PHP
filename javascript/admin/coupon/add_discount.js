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
            bookApply = [];
            //selectAll = [];
            originalBookApply = [];
            textareaDefaultValue = '';
      });

      $('#addModal').on('show.bs.modal', function ()
      {
            bookApply = [];
            //selectAll = [];
            originalBookApply = [];
            textareaDefaultValue = '';
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
      {
            textareaDefaultValue = '';
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
                        <p class="form-label">Books Applied:</p>
                        <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off" onclick="selectAllBookAddModal(event)" data-default-check-state=false>
                        <label class="btn btn-outline-success btn-sm" for="btncheck1">All Books</label>
                        <textarea readonly class="form-control pointer mt-2" id="couponBookApply" onclick="chooseBook()" rows="5"></textarea>
                  </div>`)
            );
      }
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

function selectAllBookAddModal(e)
{
      $('#couponBookApply').prop('disabled', e.target.checked);
      if (e.target.checked)
      {
            bookApply = [];
            //selectAll = [];
            $('#couponBookApply').val('');
            $('#couponBookApply').removeClass('pointer');
      } else
            $('#couponBookApply').addClass('pointer');
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
            const start = encodeData($('#couponStartDate').val());
            const end = encodeData($('#couponEndDate').val());

            if (!name)
            {
                  reportCustomValidity($('#couponName').get(0), 'Missing coupon name!');
                  return;
            }
            else if (name.length > 255)
            {
                  reportCustomValidity($('#couponName').get(0), 'Coupon name must be at most 255 characters long or less!');
                  return;
            }

            if (discount === '')
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Missing discount percentage value!');
                  return;
            }
            if (typeof discount !== 'number' || isNaN(discount) || discount <= 0 || discount > 100)
            {
                  reportCustomValidity($('#couponPercentage').get(0), 'Discount percentage value invalid!');
                  return;
            }

            const startDate = new Date(start);
            const endDate = new Date(end);
            const today = new Date();
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            if (!start)
            {
                  reportCustomValidity($('#couponStartDate').get(0), 'Missing start date!');
                  return;
            }
            else if (startDate < today)
            {
                  reportCustomValidity($('#couponStartDate').get(0), 'Start date must be after or the same day as today!');
                  return;
            }

            if (!end)
            {
                  reportCustomValidity($('#couponEndDate').get(0), 'Missing end date!');
                  return;
            }
            else if (endDate < today)
            {
                  reportCustomValidity($('#couponEndDate').get(0), 'End date must be after or the same day as today!');
                  return;
            }

            if (startDate > endDate)
            {
                  reportCustomValidity($('#couponStartDate').get(0), 'Start date must be before or the same day as end date!');
                  return;
            }

            $.ajax({
                  url: '/ajax_service/admin/coupon/add_discount.php',
                  type: 'POST',
                  data: {
                        type: type,
                        name: name,
                        discount: discount,
                        start: start,
                        end: end,
                        allBook: $('#btncheck1').prop('checked'),
                        bookApply: bookApply.length ? ((bookApply.filter(str => str.trim() !== '')).map(str => encodeData(str))).join(',') : ''
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
            else if (name.length > 255)
            {
                  reportCustomValidity($('#couponName').get(0), 'Coupon name must be at most 255 characters long or less!');
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
                  url: '/ajax_service/admin/coupon/add_discount.php',
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
            else if (name.length > 255)
            {
                  reportCustomValidity($('#couponName').get(0), 'Coupon name must be at most 255 characters long or less!');
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
                  url: '/ajax_service/admin/coupon/add_discount.php',
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