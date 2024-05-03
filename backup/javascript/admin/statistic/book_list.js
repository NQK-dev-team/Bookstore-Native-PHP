$(document).ready(function ()
{
      initToolTip();

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $("#search_book_form").submit(function (e)
      {
            e.preventDefault();
            selectBookEntry();
      });

      $('#searchCategoryForm,#searchAuthorForm,#searchPublisherForm').submit(function (e)
      {
            e.preventDefault();
            selectBookEntry();
      });

      $('#categoryDropDown,#authorDropDown,#publisherDropDown').on('hidden.bs.dropdown', function ()
      {
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

      fetchBookList();

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
                                    $(`<li class='dropdownHover pointer' onclick='chooseCategory(event)'>${ data.query_result[i].name }</li>`)
                              );
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

function fetchBookList()
{
      const entry = parseInt(encodeData($('#book_entry_select').val()));
      const search = encodeData($('#search_book').val());
      const listOffset = parseInt(encodeData($('#book_list_offset').text()));
      const status = $('#flexSwitchCheckDefault').prop('checked');
      const category = encodeData($('#categoryInput').val());
      const start = encodeData($('#startDateInput').val());
      const end = encodeData($('#endDateInput').val());
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

      {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const today = new Date();
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            if (!start)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Missing start date!');
                  return;
            }
            else if (startDate > today)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Start date must be before or the same day as today!');
                  return;
            }

            if (!end)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Missing end date!');
                  return;
            }
            else if (endDate > today)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('End date must be before or the same day as today!');
                  return;
            }

            if (startDate > endDate)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Start date must be before or the same day as end date!');
                  return;
            }
      }

      const nextBtnDisabledProp = $('#book_next_button').prop('disabled');
      const prevBtnDisabledProp = $('#book_prev_button').prop('disabled');

      $.ajax({
            url: '/ajax_service/admin/statistic/get_book_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, status: status, search: search, category: category, start: start, end: end, author, publisher },
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
                        $('#book_total_entries').text(data.query_result[1]);

                        $('#book_prev_button').prop('disabled', listOffset === 1);
                        $('#book_next_button').prop('disabled', listOffset * entry >= data.query_result[1]);

                        $('#book_table_body').empty();
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append(
                                    $(`<td class=\"align-middle\"><img ${ data.query_result[0][i].imagePath } alt=\"book image\" class=\"book_image\"></img></td>`)
                              );
                              trElem.append($(`<td class=\"col-2 align-middle\">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].edition }</td>`));
                              trElem.append($(`<td class=\"align-middle text-nowrap\">${ data.query_result[0][i].isbn }</td>`));

                              if (data.query_result[0][i].author.length)
                              {
                                    let div = $('<div>').addClass('d-flex').addClass('flex-column');
                                    for (let j = 0; j < data.query_result[0][i].author.length; j++)
                                    {
                                          if (data.query_result[0][i].author.length === 1)
                                          {
                                                div.append($('<p>').addClass('mb-0').addClass('text-nowrap').text(data.query_result[0][i].author[j]));
                                          }
                                          else
                                          {
                                                div.append($('<p>').addClass('mb-0').addClass('text-nowrap').text(data.query_result[0][i].author[j]));
                                          }
                                    }
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(div));
                              }
                              else
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').text('N/A'));

                              if (data.query_result[0][i].category.length)
                              {
                                    let div = $('<div>').addClass('d-flex').addClass('flex-column');
                                    for (let j = 0; j < data.query_result[0][i].category.length; j++)
                                    {
                                          if (data.query_result[0][i].category.length === 1)
                                          {
                                                div.append($(`<p class='mb-0 text-nowrap'>
                                                      ${ data.query_result[0][i].category[j].name }&nbsp;
                                                      <i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                          else
                                          {
                                                div.append($(`<p class='text-nowrap'>
                                                      ${ data.query_result[0][i].category[j].name }&nbsp;
                                                      <i class="bi bi-question-circle help" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                    }
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(div));
                              }
                              else
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').text('N/A'));

                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(
                                    $('<div>').addClass('d-flex').addClass('flex-column').append(
                                          $('<p>').text(data.query_result[0][i].publisher)
                                    ).append(
                                          $('<p>').addClass('text-nowrap').text(data.query_result[0][i].publishDate)
                                    )
                              ));

                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(
                                    $('<div>').addClass('truncate').text(data.query_result[0][i].description))
                              );

                              trElem.append($('<td>').addClass('align-middle col-1').append(
                                    $(`<span class='text-nowrap'><span class='text-warning'>${ displayRatingStars(data.query_result[0][i].avgRating) }</span>&nbsp;(${ data.query_result[0][i].avgRating })</span>`)
                              ));

                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(
                                    $(`<div class="d-flex flex-column">`).append(
                                          $(`<p class='text-nowrap'>Hardcover: ${ data.query_result[0][i].physicalCopy.price } (in stock: ${ data.query_result[0][i].physicalCopy.inStock })</p>`)
                                    ).append(
                                          $(`<p class='text-nowrap'>E-book: ${ data.query_result[0][i].fileCopy.price } <a title=\"${ data.query_result[0][i].fileCopy.filePath !== '' ? "read PDF file" : 'no PDF file' }\" ${ data.query_result[0][i].fileCopy.filePath !== '' ? "target='_blank'" : '' } ${ data.query_result[0][i].fileCopy.filePath } alt='${ data.query_result[0][i].fileCopy.filePath !== '' ? 'PDF file' : 'No PDF file' }'>
                                          <i class=\"bi bi-file-earmark-fill text-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ data.query_result[0][i].fileCopy.filePath !== '' ? 'Read file' : 'No PDF file' }\"></i>
                                          </a></p>`)
                                    )
                              ));

                              trElem.append($('<td>').addClass('align-middle text-nowrap').text(`${ data.query_result[0][i].totalSold } ${ data.query_result[0][i].totalSold ? (data.query_result[0][i].totalSold === '1' ? 'copy' : 'copies') : '' }`));


                              $('#book_table_body').append(trElem);
                        }

                        initToolTip();

                        if (listOffset > 1 && !data.query_result[0].length)
                        {
                              changeBookList(false);
                        }
                  }
            },

            error: function (err)
            {
                  $('#book_next_button').prop('disabled', nextBtnDisabledProp);
                  $('#book_prev_button').prop('disabled', prevBtnDisabledProp);

                  
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

function changeBookList(isNext)
{
      const entry = parseInt($('#book_entry_select').val());
      const currentOffset = parseInt($('#book_list_offset').text());
      const numberOfEntries = parseInt($('#book_total_entries').text());

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

function updateSwitchLabel()
{
      if ($('#flexSwitchCheckDefault').prop('checked'))
            $('#switch_label').text('Choose active books').addClass('text-success').removeClass('text-secondary');
      else
            $('#switch_label').text('Choose inactive books').addClass('text-secondary').removeClass('text-success');
      selectBookEntry();
}