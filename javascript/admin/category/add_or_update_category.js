let update_id = null, originalName = null, originalDescription = null;

$(document).ready(function ()
{
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

function updateCategory()
{
      clearAllCustomValidity();

      const name = encodeData($('#categoryName').val());
      const description = encodeData($('#categoryDescription').val());

      $("#updateModal").modal('hide');

      if (name === '')
      {
            reportCustomValidity($('#categoryName').get(0), 'Category name is empty!');
            return;
      }
      else if (name.length > 255)
      {
            reportCustomValidity($('#categoryName').get(0), 'Category name must be at most 255 characters long or less!');
            return;
      }

      if (description.length > 500)
      {
            reportCustomValidity($('#categoryDescription').get(0), 'Category description must be at most 500 characters long or less!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/category/update_category.php',
            method: 'PUT',
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

      $("#addModal").modal('hide');

      if (name === '')
      {
            reportCustomValidity($('#categoryName').get(0), 'Category name is empty!');
            return;
      }
      else if (name.length > 255)
      {
            reportCustomValidity($('#categoryName').get(0), 'Category name must be at most 255 characters long or less!');
            return;
      }

      if (description.length > 500)
      {
            reportCustomValidity($('#categoryDescription').get(0), 'Category description must be at most 500 characters long or less!');
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