let DELETE_ID = null, DEACTIVATE_ID = null, ACTIVATE_ID = null;

$(document).ready(function ()
{
      initToolTip();

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
      const listOffset = parseInt(sanitize($('#list_offset').text()));
      const status = parseBool($('#flexSwitchCheckDefault').prop('checked'));

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

      if (typeof status !== 'boolean' || (status !== true && status !== false))
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Book Status` data type invalid!');
            return;
      }

      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

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
                  console.log(data);
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#next_button').prop('disabled', nextBtnDisabledProp);
                  $('#prev_button').prop('disabled', prevBtnDisabledProp);
                  $('#list_offset').prop('disabled', true);

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        $('#error_message').text('');

                        $('#start_entry').text(data.query_result[1] ? (listOffset - 1) * entry + 1 : 0);
                        $('#end_entry').text(listOffset * entry <= data.query_result[1] ? listOffset * entry : data.query_result[1]);
                        $('#total_entries').text(data.query_result[1]);

                        $('#next_button').prop('disabled', $('#next_button').prop('disabled') || listOffset * entry >= data.query_result[1]);

                        $('#table_body').empty();
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class=\"align-middle\">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append(
                                    $(`<td class=\"align-middle\"><img src=\"${ data.query_result[0][i].imagePath }\" alt=\"book image\" class=\"book_image\"></img></td>`)
                              );
                              trElem.append($(`<td class=\"col-2 align-middle\">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].edition }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].isbn }</td>`));
                              trElem.append($(`<td class=\"align-middle\">${ data.query_result[0][i].ageRestriction }</td>`));

                              if (data.query_result[0][i].author.length)
                              {
                                    let div = $('<div>').addClass('d-flex').addClass('flex-column');
                                    for (let j = 0; j < data.query_result[0][i].author.length; j++)
                                    {
                                          if (data.query_result[0][i].author.length === 1)
                                          {
                                                div.append($('<p>').addClass('mb-0').text(data.query_result[0][i].author[j]));
                                          }
                                          else
                                          {
                                                div.append($('<p>').addClass('mb-0').text(data.query_result[0][i].author[j]));
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
                                                div.append($(`<p class='mb-0'>
                                                      ${ data.query_result[0][i].category[j].name }
                                                      <i class="bi bi-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                          else
                                          {
                                                div.append($(`<p>
                                                      ${ data.query_result[0][i].category[j].name }
                                                      <i class="bi bi-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[0][i].category[j].description ? data.query_result[0][i].category[j].description : 'N/A' }"></i>
                                                </p>`));
                                          }
                                    }
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(div));
                              }
                              else
                                    trElem.append($('<td>').addClass('align-middle').addClass('col-1').text('N/A'));

                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(
                                    $('<div>').addClass('d-flex').addClass('flex-column').append(
                                          $('<a>').text(data.query_result[0][i].publisher).attr('href', data.query_result[0][i].publisherLink).attr('alt', 'publisher link').addClass('mb-3').attr('target', '_blank')
                                    ).append(
                                          $('<p>').text(data.query_result[0][i].publishDate)
                                    )
                              ));

                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').text(data.query_result[0][i].description ? data.query_result[0][i].description : 'N/A'));
                              trElem.append($('<td>').addClass('align-middle').append(
                                    $(`<svg fill='#ffee00' width='24px' height='24px' viewBox='0 0 1024 1024' xmlns='http://www.w3.org/2000/svg' class='icon' stroke='#ffee00'><g id='SVGRepo_bgCarrier' stroke-width='0'></g><g id='SVGRepo_tracerCarrier' stroke-linecap='round' stroke-linejoin='round'></g><g id='SVGRepo_iconCarrier'> <path d='M908.1 353.1l-253.9-36.9L540.7 86.1c-3.1-6.3-8.2-11.4-14.5-14.5-15.8-7.8-35-1.3-42.9 14.5L369.8 316.2l-253.9 36.9c-7 1-13.4 4.3-18.3 9.3a32.05 32.05 0 0 0 .6 45.3l183.7 179.1-43.4 252.9a31.95 31.95 0 0 0 46.4 33.7L512 754l227.1 119.4c6.2 3.3 13.4 4.4 20.3 3.2 17.4-3 29.1-19.5 26.1-36.9l-43.4-252.9 183.7-179.1c5-4.9 8.3-11.3 9.3-18.3 2.7-17.5-9.5-33.7-27-36.3z'></path> </g></svg>`)
                              ).append(
                                    $(`<span>`).text(data.query_result[0][i].avgRating)
                              ));
                              trElem.append($('<td>').addClass('align-middle').addClass('col-1').append(
                                    $(`<div class="d-flex flex-column">`).append(
                                          $(`<p>Physical: $${ data.query_result[0][i].physicalCopy.price } (in stock: ${ data.query_result[0][i].physicalCopy.inStock })</p>`)
                                    ).append(
                                          $(`<p>PDF: $${ data.query_result[0][i].fileCopy.price } <a target='_blank' href=\"${ data.query_result[0][i].fileCopy.filePath }\" alt='PDF file'>
                                          <i class=\"bi bi-file-earmark-fill text-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Read file\"></i>
                                          </a></p>`)
                                    )
                              ));

                              trElem.append(
                                    $(`<td class='align-middle'>
                                                      <div class='d-flex flex-lg-row flex-column'>
                                                            <a class='btn btn-info' href='./edit-book?id=${ data.query_result[0][i].id }' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
                                                                  <i class=\"bi bi-pencil text-white\"></i>
                                                            </a>
                                                            <button data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactivate' : 'Activate' }\" onclick='${ status ? 'confirmDeactivateBook' : 'confirmActivateBook' }(\"${ data.query_result[0][i].id }\")' class='btn ${ status ? 'btn-danger' : 'btn-success' } ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactive' : 'Activate' }\">
                                                                  <i class="bi bi-power text-white"></i>
                                                            </button>
                                                            <button data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\" onclick='confirmDeleteBook(\"${ data.query_result[0][i].id }\")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0'>
                                                                  <i class=\"bi bi-trash text-white\"></i>
                                                            </button>
                                                      </div>
                                                </td>`));

                              $('#table_body').append(trElem);
                        }

                        initToolTip();
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
            $('#list_offset').text(currentOffset - 1);
            $('#prev_button').prop('disabled', currentOffset <= 2);
      }
      fetchBookList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      $('#end_entry').text($('#entry_select').val());
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

function confirmDeactivateBook(id)
{
      DEACTIVATE_ID = id;
}

function deactivateBook()
{

}

function confirmActivateBook(id)
{
      ACTIVATE_ID = id;
}

function activateBook()
{

}

$("#search_form").submit(function (e)
{
      e.preventDefault();
      selectEntry();
});
