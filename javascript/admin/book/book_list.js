let DELETE_ID = null;

$(document).ready(function ()
{
      // Attach a function to execute when the modal is fully hidden
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });
});

function fetchBookList()
{
      const entry = parseInt(sanitize($('#entry_select').val()));
      const search = sanitize($('#search_book').val());
      const listOffset = parseInt(sanitize($('#list_offset').val()));
      const status = parseBool($('#flexSwitchCheckDefault').prop('checked'));

      if (entry < 0 || typeof entry !== 'number' || entry === NaN)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Number Of Entries` invalid!');
            return;
      }

      if (listOffset <= 0 || typeof listOffset !== 'number' || listOffset === NaN)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `List Number` invalid!');
            return;
      }

      if ((status !== true && status !== false) || typeof status !== 'boolean')
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Book Status` invalid!');
            return;
      }

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/book/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, status: status, search: search },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');


            },
            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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
      const entry = parseInt(sanitize($('#entry_select').val()));
      const currentOffset = parseInt(sanitize($('#list_offset').val()));
      const numberOfEntries = parseInt(sanitize($('#entries_number').text()));

      if (isNext)
      {
            $('#prev_button').removeAttr('disabled');
            $('#list_offset').val(currentOffset + 1);
            if ((currentOffset + 1) * entry >= numberOfEntries)
                  $('#next_button').attr('disabled', true);
      }
      else
      {
            $('#next_button').removeAttr('disabled');
            $('#list_offset').val(currentOffset - 1);
            if (currentOffset <= 2)
                  $('#prev_button').attr('disabled', true);
      }
      fetchBookList();
}

function selectEntry()
{
      $('#list_offset').val(1);
      $('#prev_button').attr('disabled', true);
      $('#entries_number').text(sanitize($('#entry_select').val()));
      fetchBookList();
}

function updateSwitchLabel()
{
      if ($('#flexSwitchCheckDefault').prop('checked'))
            $('#switch_label').text('Choose active books').addClass('text-success').removeClass('text-secondary');
      else
            $('#switch_label').text('Choose inactive books').addClass('text-secondary').removeClass('text-success');
      selectEntry();
}

function confirmDeleteBook(id)
{
      DELETE_ID = id;
}

function deleteBook()
{

}

$("#search_form").submit(function (e)
{
      e.preventDefault();
      fetchBookList();
});
