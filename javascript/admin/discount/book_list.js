let bookApply = [], selectAll = [];

$(document).ready(function ()
{
      $('#addModal').on('hidden.bs.modal', function ()
      {
            bookApply = [];
            selectAll = [];
      });

      $('#searchCategoryForm').submit(function (e)
      {
            e.preventDefault();
            selectBookEntry();
      });

      $('#categoryInput').on('input', function ()
      {
            let filter = $(this).val().toUpperCase();

            $('.categories li').each(function ()
            {
                  let txtValue = $(this).text();
                  if (txtValue.toUpperCase().indexOf(filter) > -1)
                  {
                        $(this).show();
                  } else
                  {
                        $(this).hide();
                  }
            });
      });

      $("#book_search_form").submit(function (e)
      {
            e.preventDefault();
            selectBookEntry();
      });

      $('#categoryDropDown').on('hidden.bs.dropdown', function ()
      {
            selectBookEntry();
      });
});

function fetchBookList()
{
      const entry = parseInt(encodeData($('#book_entry_select').val()));
      const search = encodeData($('#searchBookInput').val());
      const listOffset = parseInt(encodeData($('#book_list_offset').text()));
      const category = encodeData($('#categoryInput').val());

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

      const nextBtnDisabledProp = $('#book_next_button').prop('disabled');
      const prevBtnDisabledProp = $('#book_prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/discount/get_book_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search, category: category },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#book_list_offset').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#book_start_entry').text(data.query_result[1] ? (listOffset - 1) * entry + 1 : 0);
                        $('#book_end_entry').text(listOffset * entry <= data.query_result[1] ? listOffset * entry : data.query_result[1]);
                        $('#total_book_entries').text(data.query_result[1]);

                        $('#book_prev_button').prop('disabled', listOffset === 1);
                        $('#book_next_button').prop('disabled', listOffset * entry >= data.query_result[1]);

                        $('#book_table_body').empty();
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class=\"align-middle text-center\"><input type='checkbox' data-book-id='${ data.query_result[0][i].id }' ${ bookApply.includes(data.query_result[0][i].id) ? 'checked' : '' } class='pointer' name='check' value="${ data.query_result[0][i].name } - ${ data.query_result[0][i].edition }" onclick='addToList(event,"${ data.query_result[0][i].id }")'></td>`));
                              trElem.append($(`<td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append($(`<td class=\"col-4 align-middle\">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].edition }</td>`));

                              if (data.query_result[0][i].category.length)
                              {
                                    let div = $('<div>').addClass('d-flex').addClass('flex-column');
                                    for (let j = 0; j < data.query_result[0][i].category.length; j++)
                                    {
                                          if (data.query_result[0][i].category.length === 1)
                                          {
                                                div.append($(`<p class='mb-0'>
                                                      ${ data.query_result[0][i].category[j].name }
                                                      <i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                          else
                                          {
                                                div.append($(`<p>
                                                      ${ data.query_result[0][i].category[j].name }
                                                      <i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                    }
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-3').append(div));
                              }
                              else
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-3').text('N/A'));


                              trElem.append(
                                    $(`<td class='align-middle'><a href='/admin/book/edit-book?id=${ data.query_result[0][i].id }' alt='book detail' class='btn btn-sm btn-info text-white'>Detail</a></td>`));

                              $('#book_table_body').append(trElem);
                        }

                        selectAll = [];
                        if (data.query_result[1])
                        {
                              if ($('input[name="check"]:checked').length === $('input[name="check"]').length)
                              {
                                    const listOffset = parseInt(encodeData($('#book_list_offset').text()));

                                    if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
                                    {
                                          $('#errorModal').modal('show');
                                          $('#error_message').text('Selected `List Number` data type invalid!');
                                          return;
                                    }

                                    if (!selectAll.includes(listOffset))
                                    {
                                          selectAll.push(listOffset);
                                          $('#checkAll').prop('checked', true);
                                    }

                              }
                              else
                                    $('#checkAll').prop('checked', false);
                        } else
                              $('#checkAll').prop('checked', false);

                        //$('#checkAll').val(listOffset).prop('checked', selectAll.includes(listOffset));

                        initToolTip();

                        if (listOffset > 1 && !data.query_result[0].length)
                        {
                              changeBookList(false);
                        }
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#book_next_button').prop('disabled', nextBtnDisabledProp);
                  $('#book_prev_button').prop('disabled', prevBtnDisabledProp);
                  $('#book_list_offset').prop('disabled', true);

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

function selectAllBook(e)
{
      const arr = $('#couponBookApply').val() !== '' ? $('#couponBookApply').val().split(';').map(x => x.trim()) : [];

      if (e.target.checked)
      {
            selectAll.push(parseInt(e.target.value));

            $('input[type="checkbox"][name="check"]').each(function ()
            {
                  $(this).prop('checked', true);
                  if (!arr.includes($(this).attr('value')))
                  {
                        arr.push($(this).attr('value'));
                        arr.sort();
                  }
                  if (!bookApply.includes($(this).attr('data-book-id')))
                        bookApply.push($(this).attr('data-book-id'));
            });
      }
      else
      {
            selectAll.splice(selectAll.indexOf(parseInt(e.target.value)), 1);

            $('input[type="checkbox"][name="check"]').each(function ()
            {
                  $(this).prop('checked', false);
                  if (arr.includes($(this).attr('value')))
                  {
                        arr.splice(arr.indexOf($(this).attr('value')), 1);
                        arr.sort();
                  }
                  if (bookApply.includes($(this).attr('data-book-id')))
                        bookApply.splice(bookApply.indexOf($(this).attr('data-book-id')), 1);
            });
      }
      $('#couponBookApply').val(arr.join('; '));
      $('#totalSelected').text(bookApply.length);
}

function addToList(e, id)
{
      const arr = $('#couponBookApply').val() !== '' ? $('#couponBookApply').val().split(';').map(x => x.trim()) : [];
      if (e.target.checked)
      {
            if (!arr.includes(e.target.value))
            {
                  arr.push(e.target.value);
                  arr.sort();
            }

            if (!bookApply.includes(id))
                  bookApply.push(id);

            if ($('input[name="check"]:checked').length === $('input[name="check"]').length)
            {
                  const listOffset = parseInt(encodeData($('#book_list_offset').text()));

                  if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text('Selected `List Number` data type invalid!');
                        return;
                  }

                  if (!selectAll.includes(listOffset))
                  {
                        selectAll.push(listOffset);
                        $('#checkAll').prop('checked', true);
                  }

            }
      }
      else
      {
            const listOffset = parseInt(encodeData($('#book_list_offset').text()));

            if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Selected `List Number` data type invalid!');
                  return;
            }

            if (arr.includes(e.target.value))
            {
                  arr.splice(arr.indexOf(e.target.value), 1);
                  arr.sort();
            }

            if (bookApply.includes(id))
                  bookApply.splice(bookApply.indexOf(id), 1);

            if (selectAll.includes(listOffset))
                  selectAll.splice(selectAll.indexOf(listOffset), 1);
            $('#checkAll').prop('checked', false);
      }
      $('#couponBookApply').val(arr.join('; '));
      $('#totalSelected').text(bookApply.length);
}

function chooseCategory(e)
{
      $('#categoryInput').val(e.target.innerText);
      $('#categoryInput').trigger('input');
      selectBookEntry();
}

function changeBookList(isNext)
{
      const entry = parseInt($('#book_entry_select').val());
      const currentOffset = parseInt($('#book_list_offset').text());
      const numberOfEntries = parseInt($('#total_book_entries').text());

      if (isNext)
      {
            $('#book_prev_button').prop('disabled', false);
            $('#book_list_offset').text(currentOffset + 1);
            $('#book_next_button').prop('disabled', (currentOffset + 1) * entry >= numberOfEntries);
      }
      else
      {
            $('#book_next_button').prop('disabled', false);
            $('#book_list_offset').text((currentOffset - 1) ? currentOffset - 1 : 1);
            $('#book_prev_button').prop('disabled', currentOffset <= 2);
      }
      fetchBookList();
}

function selectBookEntry()
{
      $('#book_list_offset').text(1);
      $('#book_prev_button').attr('disabled', true);
      $('#book_next_button').attr('disabled', false);
      fetchBookList();
}

function chooseBook()
{
      selectBookEntry();
      $('#chooseBookModal').modal('show');
      $('#totalSelected').text(bookApply.length);
}

function clearForm()
{
      bookApply = [];
      selectAll = [];
}