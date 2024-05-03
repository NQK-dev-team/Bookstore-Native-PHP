$(document).ready(function ()
{
      initToolTip();

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#search_form').submit(function (event)
      {
            event.preventDefault();
            selectEntry();
      });

      fetchCategoryList();
});

function fetchCategoryList()
{
      const entry = parseInt(encodeData($('#entry_select').val()));
      const search = encodeData($('#search_category').val());
      const listOffset = parseInt(encodeData($('#list_offset').text()));

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Number of entries of categories invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Category list number invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/category/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search },
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
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class="align-middle">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append($(`<td class="col-1 align-middle">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class="align-middle"><div class="truncate">${ data.query_result[0][i].description ? data.query_result[0][i].description : 'N/A' }</div></td>`));
                              trElem.append(
                                    $(`
                                    <td class="align-middle col-1">
                                          <div class='d-flex flex-lg-row flex-column'>
                                                <button title="edit category" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit" class="btn btn-info btn-sm me-lg-2" onclick="openEditModal('${ data.query_result[0][i].id }')"><i class="bi bi-pencil text-white"></i></button>
                                                <button title="delete category" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" class="btn btn-danger btn-sm mt-2 mt-lg-0" onclick="confirmDelete('${ data.query_result[0][i].id }')"><i class="bi bi-trash text-white"></i></button>
                                          </div>
                                    </td>
                                    `)
                              );

                              $('#table_body').append(trElem);
                        }

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
      fetchCategoryList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      fetchCategoryList();
}