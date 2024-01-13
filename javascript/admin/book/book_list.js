function fetchBookList()
{
      console.log("called");
}

function changeList(isNext)
{
      const currentEntry = parseInt($('#list_offset').val());

      if (isNext)
      {
            $('#prev_button').removeAttr('disabled');
      }
      else
      {
            $('#next_button').removeAttr('disabled');
      }
      fetchBookList();
}

$("#search_form").submit(function (e)
{
      e.preventDefault();
      fetchBookList();
});