let update_id = null;
let bookArr = [];

$(document).ready(function ()
{
      $('#updateCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmUpdateModal').modal('show');
      });

      $('#updateModal').on('hidden.bs.modal', function ()
      {
            $('#updateCouponForm').empty();
            update_id = null;
            bookApply = [];
            selectAll = [];
            originalBookApply = [];
      });
});

function selectAllBookUpdateModal(e)
{
      $('#couponBookApply').prop('disabled', e.target.checked);
      if (e.target.checked)
      {
            bookApply = [];
            selectAll = [];
            $('#couponBookApply').val('');
      }
      else
      {
            $('#couponBookApply').val(bookArr.length ? bookArr.join(', ') : '');
      }
}

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

            $.ajax({
                  url: '/ajax_service/admin/coupon/get_discount_detail.php',
                  method: 'GET',
                  data: { id: encodeData(id), type: type },
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
                              if (!data.query_result.applyForAll)
                              {
                                    bookArr = data.query_result.bookApply.map(elem => `${ elem.name } - ${ elem.edition }`);
                                    bookApply = data.query_result.bookApply.map(elem => elem.id);
                              }
                              $('#updateCouponForm').append(
                                    $(`<div>
                                          <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name" value="${ data.query_result.name }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value" value="${ data.query_result.discount }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponStartDate" class="form-label">Start Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="date" class="form-control" id="couponStartDate" value="${ data.query_result.startDate }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponEndDate" class="form-label">End Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="date" class="form-control" id="couponEndDate" value="${ data.query_result.endDate }">
                                    </div>
                                    <div class='mt-2'>
                                          <p class="form-label">Books Applied:</p>
                                          <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off" onclick="selectAllBookUpdateModal(event)" ${ data.query_result.applyForAll ? 'checked' : '' } data-default-check-state=${ data.query_result.applyForAll }>
                                          <label class="btn btn-outline-success btn-sm" for="btncheck1">All Books</label>
                                          <input readonly type="text" class="form-control pointer mt-2" id="couponBookApply" onclick="chooseBook()" ${ data.query_result.applyForAll ? 'disabled' : '' } value="${ bookArr.length ? bookArr.join(', ') : '' }">
                                    </div>`)
                              );

                              $('#updateModal').modal('show');
                        }
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
            })
      }
      else if (type === 2)
      {
            update_id = id;

            $.ajax({
                  url: '/ajax_service/admin/coupon/get_discount_detail.php',
                  method: 'GET',
                  data: { id: encodeData(id), type: type },
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
                              $('#updateCouponForm').append(
                                    $(`<div>
                                          <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name" value="${ data.query_result.name }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="number" class="form-control" id="couponPercentage" placeholder="Enter value" value="${ data.query_result.discount }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponPoint" class="form-label">Accumulated Points:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="number" class="form-control" id="couponPoint" placeholder="Enter value" value="${ data.query_result.point }">
                                    </div>`)
                              );

                              $('#updateModal').modal('show');
                        }
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
            })
      }
      else if (type === 3)
      {
            update_id = id;

            $.ajax({
                  url: '/ajax_service/admin/coupon/get_discount_detail.php',
                  method: 'GET',
                  data: { id: encodeData(id), type: type },
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
                              $('#updateCouponForm').append(
                                    $(`<div>
                                          <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name" value="${ data.query_result.name }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="number" class="form-control" id="couponPercentage" placeholder="Enter value" value="${ data.query_result.discount }">
                                    </div>
                                    <div class='mt-2'>
                                          <label for="couponPoint" class="form-label">Accumulated Points:<span class="fw-bold text-danger">&nbsp;*</span></label>
                                          <input type="number" class="form-control" id="couponPoint" placeholder="Enter value" value="${ data.query_result.numberOfPeople }">
                                    </div>`)
                              );

                              $('#updateModal').modal('show');
                        }
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
            })
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