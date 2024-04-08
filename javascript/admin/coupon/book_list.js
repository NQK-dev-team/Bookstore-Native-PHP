let bookApply = [];
//let selectAll = [];
let originalBookApply = [];
let textareaDefaultValue = '';

$(document).ready(function ()
{
      $('#searchCategoryForm,#searchAuthorForm,#searchPublisherForm').submit(function (e)
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

      $('#authorInput').on('input', function ()
      {
            let filter = $(this).val().toUpperCase();

            $('.authors li').each(function ()
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

      $('#publisherInput').on('input', function ()
      {
            let filter = $(this).val().toUpperCase();

            $('.publishers li').each(function ()
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

      $('#categoryDropDown,#authorDropDown,#publisherDropDown').on('hidden.bs.dropdown', function ()
      {
            selectBookEntry();
      });

      $.ajax({
            url: '/ajax_service/admin/book/get_category_list.php',
            method: 'GET',
            data: { search: '' },
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
                        $('#category_list').empty();
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              $('#category_list').append(
                                    $(`<li class='dropdownHover pointer my-2' onclick='chooseCategory(event)'>${ data.query_result[i].name }</li>`)
                              );
                        }
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
      });

      $.ajax({
            url: '/ajax_service/admin/book/get_author_list.php',
            method: 'GET',
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
                        $('#author_list').empty();
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              $('#author_list').append(
                                    $(`<li class='dropdownHover pointer my-2' onclick='chooseAuthor(event)'>${ data.query_result[i].name }</li>`)
                              );
                        }
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
      });

      $.ajax({
            url: '/ajax_service/admin/book/get_publisher_list.php',
            method: 'GET',
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
                        $('#publisher_list').empty();
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              $('#publisher_list').append(
                                    $(`<li class='dropdownHover pointer my-2' onclick='choosePublisher(event)'>${ data.query_result[i].name }</li>`)
                              );
                        }
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
      });
});

function fetchBookList()
{
      const entry = parseInt(encodeData($('#book_entry_select').val()));
      const search = encodeData($('#searchBookInput').val());
      const listOffset = parseInt(encodeData($('#book_list_offset').text()));
      const category = encodeData($('#categoryInput').val());
      const author = encodeData($('#authorInput').val());
      const publisher = encodeData($('#publisherInput').val());

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Number of entries of books invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Book list number invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/coupon/get_book_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search, category: category, author, publisher },
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
                        $('#book_start_entry').text(data.query_result[1] ? (listOffset - 1) * entry + 1 : 0);
                        $('#book_end_entry').text(listOffset * entry <= data.query_result[1] ? listOffset * entry : data.query_result[1]);
                        $('#total_book_entries').text(data.query_result[1]);

                        $('#book_prev_button').prop('disabled', listOffset === 1);
                        $('#book_next_button').prop('disabled', listOffset * entry >= data.query_result[1]);

                        $('#book_table_body').empty();
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class=\"align-middle text-center\"><input ${ bookApply.includes(data.query_result[0][i].id) ? 'checked' : '' } type='checkbox' data-book-id='${ data.query_result[0][i].id }' ${ bookApply.includes(data.query_result[0][i].id) ? 'checked' : '' } class='pointer' name='check' value="${ data.query_result[0][i].name } - ${ data.query_result[0][i].edition } edition" onclick='addToList(event,"${ data.query_result[0][i].id }")'></td>`));
                              trElem.append($(`<td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append($(`<td class=\"col-4 align-middle\">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].edition }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].author.join(', ') }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].publisher }</td>`));

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
                                    $(`<td class='align-middle'><a href='/admin/book/edit-book?id=${ data.query_result[0][i].id }' alt='Book detail' class='btn btn-sm btn-info text-white' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\"><i class=\"bi bi-info-circle\"></i></a></td>`));

                              $('#book_table_body').append(trElem);
                        }

                        //selectAll = [];
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

                                    // if (!selectAll.includes(listOffset))
                                    // {
                                    //       selectAll.push(listOffset);
                                    $('#checkAll').prop('checked', true);
                                    //}

                              }
                              else
                                    $('#checkAll').prop('checked', false);
                        } else
                              $('#checkAll').prop('checked', false);

                        initToolTip();

                        if (listOffset > 1 && !data.query_result[0].length)
                        {
                              changeBookList(false);
                        }
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

function selectAllBook(e)
{
      const arr = $('#couponBookApply').val() !== '' ? $('#couponBookApply').val().split('\n').map(x => x.trim()) : [];

      if (e.target.checked)
      {
            //selectAll.push(parseInt(e.target.value));

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
            //selectAll.splice(selectAll.indexOf(parseInt(e.target.value)), 1);

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
      $('#couponBookApply').val(arr.join('\n'));
      $('#totalSelected').text(bookApply.length);
}

function addToList(e, id)
{
      const arr = $('#couponBookApply').val() !== '' ? $('#couponBookApply').val().split('\n').map(x => x.trim()) : [];
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

                  // if (!selectAll.includes(listOffset))
                  // {
                  //       selectAll.push(listOffset);
                  $('#checkAll').prop('checked', true);
                  //}

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

            // if (selectAll.includes(listOffset))
            //       selectAll.splice(selectAll.indexOf(listOffset), 1);
            $('#checkAll').prop('checked', false);
      }
      $('#couponBookApply').val(arr.join('\n'));
      $('#totalSelected').text(bookApply.length);
}

function chooseCategory(e)
{
      $('#categoryInput').val(e.target.innerText);
      $('#categoryInput').trigger('input');
      selectBookEntry();
}

function chooseAuthor(e)
{
      $('#authorInput').val(e.target.innerText);
      $('#authorInput').trigger('input');
      selectBookEntry();
}

function choosePublisher(e)
{
      $('#publisherInput').val(e.target.innerText);
      $('#publisherInput').trigger('input');
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
      bookApply = [...originalBookApply];
      //selectAll = [];
      $('#couponBookApply').prop('disabled', ($('#btncheck1').attr('data-default-check-state') === 'true' || $('#btncheck1').attr('data-default-check-state') === '1'));
      if ($('#couponBookApply').prop('disabled'))
            $('#couponBookApply').removeClass('pointer');
      else
            $('#couponBookApply').addClass('pointer');
      $('#couponBookApply').val(textareaDefaultValue);
}