$(document).ready(function ()
{
      initToolTip();

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#search_form').submit(function (event)
      {
            event.preventDefault();
            selectEntry();
      });
      fetchCustomerList();
});

function fetchCustomerList()
{
      const entry = parseInt(encodeData($('#entry_select').val()));
      const search = encodeData($('#search_customer').val());
      const listOffset = parseInt(encodeData($('#list_offset').text()));
      const status = $('#flexSwitchCheckDefault').prop('checked');

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Number of entries of customers invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Customer list number invalid!');
            return;
      }

      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/customer/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search, status: status },
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');
                  $('#list_offset').prop('disabled', true);

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

                              trElem.append($(`<td class="align-middle">${ (listOffset - 1) * entry + i + 1 }</td>`));
                              trElem.append($(`<td class="col-2 align-middle">${ data.query_result[0][i].name }</td>`));
                              trElem.append($(`<td class="align-middle col-2">${ data.query_result[0][i].email }</td>`));
                              trElem.append($(`<td class="align-middle col-1">${ data.query_result[0][i].phone }</td>`));
                              trElem.append($(`<td class="align-middle col-1">${ data.query_result[0][i].dob }</td>`));
                              trElem.append($(`<td class="align-middle col-2">${ data.query_result[0][i].address }</td>`));
                              trElem.append($(`<td class="align-middle">${ data.query_result[0][i].gender }</td>`));
                              trElem.append($(`<td class="align-middle">${ data.query_result[0][i].point }</td>`));
                              if (!status)
                                    trElem.append($(`<td class="align-middle col-1">${ data.query_result[0][i].deleteTime ? data.query_result[0][i].deleteTime : 'N/A' }</td>`));
                              trElem.append(
                                    $(`
                                    <td class="align-middle col-1">
                                          <div class='d-flex flex-lg-row flex-column'>
                                          <a class='btn btn-sm btn-info text-white' href='./detail?id=${ data.query_result[0][i].id }' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Detail\"><i class=\"bi bi-info-circle\"></i></a>
                                          ${ data.query_result[0][i].email === 'N/A' ? '' : (`
                                          ${ status ?
                                                `<button onclick='openDeactivateModal(\"${ data.query_result[0][i].id }\")' class='btn btn-sm btn-danger text-white ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Deactivate\"><i class=\"bi bi-power\"></i></button>`
                                                :
                                                `<button onclick='openActivateModal(\"${ data.query_result[0][i].id }\")' class='btn btn-sm btn-success text-white ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Activate ${ data.query_result[0][i].deleteTime ? 'and cancel deletion' : '' }\"><i class=\"bi bi-power\"></i></button>` }
                                          ${ data.query_result[0][i].deleteTime ? '' : `<button onclick='openDeleteModal(\"${ data.query_result[0][i].id }\")' class='btn btn-sm btn-danger text-white ms-lg-2 mt-2 mt-lg-0' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\"><i class=\"bi bi-trash3-fill\"></i></button>` }
                                          `) }
                                          </div>
                                    </td>
                                    `)
                              );

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
            $('#list_offset').text((currentOffset - 1) ? currentOffset - 1 : 1);
            $('#prev_button').prop('disabled', currentOffset <= 2);
      }
      fetchCustomerList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      fetchCustomerList();
}

function updateSwitchLabel()
{
      if ($('#flexSwitchCheckDefault').prop('checked'))
      {
            $('#switch_label').text('Choose active customers').addClass('text-success').removeClass('text-secondary');
            $('#table_header').empty();
            $('#table_header').append($(`<tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Phone Number</th>
                                                <th scope="col">Date of Birth</th>
                                                <th scope="col">Address</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col" class='text-nowrap'>Accumulated Points</th>
                                                <th scope="col">Action</th>
                                          </tr>`));
      }
      else
      {
            $('#switch_label').text('Choose inactive customers').addClass('text-secondary').removeClass('text-success');
            $('#table_header').empty();
            $('#table_header').append($(`<tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Phone Number</th>
                                                <th scope="col">Date of Birth</th>
                                                <th scope="col">Address</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col" class='text-nowrap'>Accumulated Points</th>
                                                <th scope="col">Delete Date</th>
                                                <th scope="col">Action</th>
                                          </tr>`));
      }
      selectEntry();
}