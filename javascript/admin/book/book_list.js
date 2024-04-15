let DELETE_ID = null, DEACTIVATE_ID = null, ACTIVATE_ID = null;

$(document).ready(function ()
{
      initToolTip();

      // Attach a function to execute when the modal is fully hidden
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#deactivateModal').on('hidden.bs.modal', function ()
      {
            DEACTIVATE_ID = null;
      });

      $('#activateModal').on('hidden.bs.modal', function ()
      {
            ACTIVATE_ID = null;
      });

      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            DELETE_ID = null;
      });

      $("#search_form").submit(function (e)
      {
            e.preventDefault();
            selectEntry();
      });

      $('#searchCategoryForm,#searchAuthorForm,#searchPublisherForm').submit(function (e)
      {
            e.preventDefault();
            selectEntry();
      });

      $('#categoryDropDown,#authorDropDown,#publisherDropDown').on('hidden.bs.dropdown', function ()
      {
            selectEntry();
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
      const entry = parseInt(encodeData($('#entry_select').val()));
      const search = encodeData($('#search_book').val());
      const listOffset = parseInt(encodeData($('#list_offset').text()));
      const status = $('#flexSwitchCheckDefault').prop('checked');
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
            url: '/ajax_service/admin/book/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, status: status, search: search, category: category, author, publisher },
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

                              trElem.append($(`<td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append(
                                    $(`<td class=\"align-middle\"><img ${ data.query_result[0][i].imagePath } alt=\"Book image\" class=\"book_image\"></img></td>`)
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

                              trElem.append(
                                    $(`<td class='align-middle col-1'>
                                                      <div class='d-flex flex-lg-row flex-column'>
                                                            <a title='go to book detail' class='btn btn-info btn-sm' href='./edit-book?id=${ data.query_result[0][i].id }' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\">
                                                                  <i class=\"bi bi-info-circle text-white\"></i>
                                                            </a>
                                                            <button title=\"${ status ? 'deactivate book' : 'activate book' }\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactivate' : 'Activate' }\" onclick='${ status ? 'confirmDeactivateBook' : 'confirmActivateBook' }(\"${ data.query_result[0][i].id }\")' class='btn ${ status ? 'btn-danger' : 'btn-success' } ms-lg-2 mt-2 mt-lg-0 btn-sm'>
                                                                  <i class="bi bi-power text-white"></i>
                                                            </button>
                                                            ${ data.query_result[0][i].can_delete ? `<button title=\"delete book\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\" onclick='confirmDeleteBook(\"${ data.query_result[0][i].id }\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm'>
                                                                  <i class=\"bi bi-trash text-white\"></i>
                                                            </button>`: '' }
                                                      </div>
                                                </td>`));

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
      fetchBookList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
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
      $('#deleteModal').modal('show');
}

function deleteBook()
{
      $.ajax({
            url: '/ajax_service/admin/book/delete_book.php',
            type: 'DELETE',
            data: {
                  id: encodeData(DELETE_ID)
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
                  fetchBookList();
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
      $('#deleteModal').modal('hide');
}

function confirmDeactivateBook(id)
{
      DEACTIVATE_ID = id;
      $('#deactivateModal').modal('show');
}

function deactivateBook()
{
      $.ajax({
            url: '/ajax_service/admin/book/update_book_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(DEACTIVATE_ID),
                  status: false
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
                        $('#deactivateModal').modal('hide');
                  }
                  fetchBookList();
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
      $('#deactivateModal').modal('hide');
}

function confirmActivateBook(id)
{
      ACTIVATE_ID = id;
      $('#activateModal').modal('show');
}

function activateBook()
{
      $.ajax({
            url: '/ajax_service/admin/book/update_book_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(ACTIVATE_ID),
                  status: true
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
                        $('#activateModal').modal('hide');
                  }
                  fetchBookList();
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
      $('#activateModal').modal('hide');
}

function chooseCategory(e)
{
      $('#categoryInput').val(e.target.innerText);
      $('#categoryInput').trigger('input');
      selectEntry();
}

function chooseAuthor(e)
{
      $('#authorInput').val(e.target.innerText);
      $('#authorInput').trigger('input');
      selectEntry();
}

function choosePublisher(e)
{
      $('#publisherInput').val(e.target.innerText);
      $('#publisherInput').trigger('input');
      selectEntry();
}