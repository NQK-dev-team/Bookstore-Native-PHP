let delete_id = null;

$(document).ready(function ()
{
      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            delete_id = null;
      });
});

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