let DELETE_ID = null;

function fetchBookList()
{
      console.log("called");

      const entry = parseInt($('#entry_select').val());
      const search = $('#search_book').val();
      const listOffset = $('#list_offset').val();
      const status = $('#flexSwitchCheckDefault').prop('checked');

      if (entry < 0)
      {

            return;
      }

      if (listOffset <= 0)
      {

            return;
      }

      if (status !== true && status !== false)
      {

            return;
      }
}

function changeList(isNext)
{
      const entry = parseInt($('#entry_select').val());
      const currentOffset = parseInt($('#list_offset').val());
      const numberOfEntries = parseInt($('#entries_number').text());

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
      $('#entries_number').text($('#entry_select').val());
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

// $(document).ready(function ()
// {
//       $('#errorModal').modal('show');
// })
