let originalRate;

$(document).ready(function ()
{
      $('#point_converion_form').on('submit', function (e)
      {
            e.preventDefault();
            changePointPolicy();
      });

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').val(originalRate);
      });

      originalRate = $('#pointConversionRate').val();
});

function resetConversion()
{
      $('#pointConversionRate').val(originalRate);
}

function changePointPolicy()
{
      const rate = $('#pointConversionRate').val() ? parseFloat(encodeData($('#pointConversionRate').val())) : '';

      if (!rate)
      {
            reportCustomValidity($('#pointConversionRate').get(0), "Please enter conversion rate value!");
      }
      else if (typeof rate !== 'number' || isNaN(rate) || rate < 0)
      {
            reportCustomValidity($('#pointConversionRate').get(0), "Conversion rate value invalid!");
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/policy/change_conversion.php',
            method: 'PATCH',
            data: { rate: rate },
            dataType: 'json',
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#successModal').modal('show');
                        originalRate = rate;
                  }
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
      })
}