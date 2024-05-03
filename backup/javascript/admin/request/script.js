$(document).ready(function ()
{
      fetchList();
});

function deleteRequest(id)
{
      $.ajax({
            url: '/ajax_service/admin/request/remove.php',
            method: 'DELETE',
            data: { id: encodeData(id) },
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
                  else
                  {
                        $('#successModal').modal('show');
                        fetchList();
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
      });

}

function fetchList()
{
      const listOffset = parseInt(encodeData($('#list_offset').text()));
      const entry = parseInt(encodeData($('#entry_select').val()));

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Number of entries invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('List number invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/request/get_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset },
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
                        $('#start_entry').text(data.query_result[1] ? (listOffset - 1) * entry + 1 : 0);
                        $('#end_entry').text(listOffset * entry <= data.query_result[1] ? listOffset * entry : data.query_result[1]);
                        $('#total_entries').text(data.query_result[1]);

                        $('#prev_button').prop('disabled', listOffset === 1);
                        $('#next_button').prop('disabled', listOffset * entry >= data.query_result[1]);

                        $('#table_body').empty();
                        let temp = '';
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              temp += `<tr>
                              <td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>
                              <td class='col align-middle text-nowrap'>${ data.query_result[0][i].name }</td>
                              <td class='col align-middle text-nowrap'>${ data.query_result[0][i].author }</td>
                              <td class='col align-middle text-nowrap'>${ data.query_result[0][i].requestTime }</td>
                              <td class='col-1 align-middle'>
                              <button onclick='deleteRequest("${ data.query_result[0][i].id }")' class='btn btn-danger btn-sm' title=\"Delete request\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete request\"><i class=\"bi bi-trash text-white\"></i></button>
                              </td>
                              </tr>`;
                        }
                        $('#table_body').append(temp);

                        initToolTip();

                        if (listOffset > 1 && !data.query_result[0].length)
                        {
                              changeList(false);
                        }
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

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      fetchList();
}

function changeList(isNext)
{
      const entry = parseInt($('#entry_select').val());
      const currentOffset = parseInt($('#list_offset').text());
      const numberOfEntries = parseInt($('#total_entries').text());

      if (isNext)
      {
            $('#prev_button').prop('disabled', false);
            $('#list_offset').text(currentOffset + 1);
            $('#next_button').prop('disabled', (currentOffset + 1) * entry >= numberOfEntries);
      }
      else
      {
            $('#next_button').prop('disabled', false);
            $('#list_offset').text((currentOffset - 1) ? currentOffset - 1 : 1);
            $('#prev_button').prop('disabled', currentOffset <= 2);
      }
      fetchList();
}