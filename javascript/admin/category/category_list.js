let delete_id = null, update_id = null, originalName = null, originalDescription = null;

$(document).ready(function ()
{
      initToolTip();

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            delete_id = null;
      });

      $('#search_form').submit(function (event)
      {
            event.preventDefault();
            selectEntry();
      });

      $('#inputModal').on('hidden.bs.modal', function ()
      {
            update_id = null;
            $('#inputModalConfirm').off('click'); // Remove the event listener
            $('#inputModalTitle').text('');
            originalName = null;
            originalDescription = null;
            $('#categoryName').val(originalName);
            $('#categoryDescription').val(originalDescription);
      });

      $('#updateSuccessModal').on('hidden.bs.modal', function ()
      {
            fetchCategoryList();
      });

      $('#addSuccessModal').on('hidden.bs.modal', function ()
      {
            fetchCategoryList();
      });
});

function fetchCategoryList()
{
      const entry = parseInt(encodeData($('#entry_select').val()));
      const search = encodeData($('#search_category').val());
      const listOffset = parseInt(encodeData($('#list_offset').text()));

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Number Of Entries` data type invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `List Number` data type invalid!');
            return;
      }

      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/category/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#list_offset').prop('disabled', true);

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
                                                <button data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit" class="btn btn-info btn-sm me-lg-2" onclick="openEditModal('${ data.query_result[0][i].id }')"><i class="bi bi-pencil text-white"></i></button>
                                                <button data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" class="btn btn-danger btn-sm mt-2 mt-lg-0" onclick="confirmDelete('${ data.query_result[0][i].id }')"><i class="bi bi-trash text-white"></i></button>
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
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#next_button').prop('disabled', nextBtnDisabledProp);
                  $('#prev_button').prop('disabled', prevBtnDisabledProp);
                  $('#list_offset').prop('disabled', true);

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

function openEditModal(id)
{
      $.ajax({
            url: '/ajax_service/admin/category/get_detail.php',
            method: 'GET',
            data: { id: encodeData(id) },
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
                        $('#inputModalTitle').text('Edit Category');
                        $('#inputModalConfirm').on('click', () => { $('#updateModal').modal('show'); });;
                        $("#inputModal").modal('show');
                        $('#categoryName').val(data.query_result.name);
                        originalName = data.query_result.name;
                        $('#categoryDescription').val(data.query_result.description);
                        originalDescription = data.query_result.description;
                        update_id = id;
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

function openAddModal()
{
      $('#inputModalTitle').text('Add Category');
      $('#inputModalConfirm').on('click', () => { $('#addModal').modal('show'); });
      $("#inputModal").modal('show');
}

function confirmDelete(id)
{
      delete_id = id;
      $("#deleteModal").modal('show');
}

function deleteCategory()
{
      $.ajax({
            url: '/ajax_service/admin/category/delete_category.php',
            type: 'DELETE',
            data: {
                  id: encodeData(delete_id)
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
                  fetchCategoryList();
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
      $("#deleteModal").modal('hide');
}

function updateCategory()
{
      clearAllCustomValidity();

      const name = encodeData($('#categoryName').val());
      const description = encodeData($('#categoryDescription').val());

      if (name === '')
      {
            $("#updateModal").modal('hide');
            reportCustomValidity($('#categoryName').get(0), 'Category name is empty!');
            return;
      }
      else if (name.length > 255)
      {
            $("#updateModal").modal('hide');
            reportCustomValidity($('#categoryName').get(0), 'Category name must be 255 characters long or less!');
            return;
      }

      if (description.length > 500)
      {
            $("#updateModal").modal('hide');
            reportCustomValidity($('#categoryDescription').get(0), 'Category description must be 500 characters long or less!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/category/update_category.php',
            method: 'POST',
            data: { id: encodeData(update_id), name: name, description: description },
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
                        $("#updateModal").modal('hide');
                        $('#inputModal').modal('hide');
                        $('#updateSuccessModal').modal('show');
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

function addCategory()
{
      clearAllCustomValidity();

      const name = encodeData($('#categoryName').val());
      const description = encodeData($('#categoryDescription').val());

      if (name === '')
      {
            $("#addModal").modal('hide');
            reportCustomValidity($('#categoryName').get(0), 'Category name is empty!');
            return;
      }
      else if (name.length > 255)
      {
            $("#addModal").modal('hide');
            reportCustomValidity($('#categoryName').get(0), 'Category name must be 255 characters long or less!');
            return;
      }

      if (description.length > 500)
      {
            $("#addModal").modal('hide');
            reportCustomValidity($('#categoryDescription').get(0), 'Category description must be 500 characters long or less!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/category/add_category.php',
            method: 'POST',
            data: { name: name, description: description },
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
                        $("#addModal").modal('hide');
                        $('#inputModal').modal('hide');
                        $('#addSuccessModal').modal('show');
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

function resetForm()
{
      $('#categoryName').val(originalName);
      $('#categoryDescription').val(originalDescription);
}