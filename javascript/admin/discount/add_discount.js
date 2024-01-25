$(document).ready(function ()
{
      $('#addCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmAddModal').modal('show');
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
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPoint" class="form-label">Accumulated Points:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPoint" min="0" placeholder="Enter value">
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
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPeople" class="form-label">Number of People:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPeople" min="0" placeholder="Enter value">
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

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}