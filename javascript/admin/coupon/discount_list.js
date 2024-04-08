$(document).ready(function ()
{
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#search_form').submit(function (event)
      {
            event.preventDefault();
            selectEntry();
      });

      initToolTip();

      fetchCouponList();
});

function updateSwitchLabel()
{
      if ($('#flexSwitchCheckDefault').prop('checked'))
            $('#switch_label').text('Choose active coupons').addClass('text-success').removeClass('text-secondary');
      else
            $('#switch_label').text('Choose inactive coupons').addClass('text-secondary').removeClass('text-success');
      selectEntry();
}

function fetchCouponList()
{
      const entry = parseInt(encodeData($('#entry_select').val()));
      const search = encodeData($('#search_coupon').val());
      const listOffset = parseInt(encodeData($('#list_offset').text()));
      const type = parseInt(encodeData($('#couponSelect').val()));
      const status = $('#flexSwitchCheckDefault').prop('checked');

      if (typeof entry !== 'number' || isNaN(entry) || entry < 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Number of entries of coupons invalid!');
            return;
      }

      if (typeof listOffset !== 'number' || isNaN(listOffset) || listOffset <= 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Coupon list number invalid!');
            return;
      }

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Coupon type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/coupon/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search, type: type, status: status },
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

                        $('#table_head').empty();
                        if (type === 1)
                        {
                              $('#table_head').append(
                                    $(`<tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Discount Percentage</th>
                                          <th scope="col">Period</th>
                                          <th scope="col">Books Applied</th>
                                          ${ status ? '<th scope="col">Status</th>' : '' }
                                          <th scope="col">Action</th>
                                    </tr>`)
                              );
                        }
                        else if (type === 2)
                        {
                              $('#table_head').append(
                                    $(`<tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Discount Percentage</th>
                                          <th scope="col">Accumulated Points</th>
                                          <th scope="col">Action</th>
                                    </tr>`)
                              );
                        }
                        else if (type === 3)
                        {
                              $('#table_head').append(
                                    $(`<tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Discount Percentage</th>
                                          <th scope="col">Number of People</th>
                                          <th scope="col">Action</th>
                                    </tr>`)
                              );
                        }


                        $('#table_body').empty();
                        for (let i = 0; i < data.query_result[0].length; i++)
                        {
                              const trElem = $('<tr>');

                              trElem.append($(`<td class="align-middle">${ (listOffset - 1) * entry + i + 1 }</td>`));

                              if (type === 1)
                              {
                                    trElem.append(
                                          $(`
                                          <td class='align-middle'>${ data.query_result[0][i].name }</td>
                                          <td class='align-middle'>${ data.query_result[0][i].discount }%</td>
                                          <td class='align-middle'>${ data.query_result[0][i].startDate } - ${ data.query_result[0][i].endDate }</td>
                                    `)
                                    );
                                    if (typeof data.query_result[0][i].applyFor === 'boolean' && data.query_result[0][i].applyFor)
                                    {
                                          trElem.append($(`<td class='align-middle col-5'><strong>All Books</strong></td>`));
                                    }
                                    else
                                    {
                                          let array = '';
                                          for (let j = 0; j < data.query_result[0][i].applyFor.length; j++)
                                          {
                                                if (data.query_result[0][i].applyFor.length === 1)
                                                      array += `<a href='/admin/book/edit-book?id=${ data.query_result[0][i].applyFor[j].id }' class='${ data.query_result[0][i].applyFor[j].status ? 'text-decoration-none' : 'text-decoration-line-through' }'>${ data.query_result[0][i].applyFor[j].name } - ${ data.query_result[0][i].applyFor[j].edition } edition</a>`;
                                                else
                                                      array += `<a href='/admin/book/edit-book?id=${ data.query_result[0][i].applyFor[j].id }' class='mb-3 ${ data.query_result[0][i].applyFor[j].status ? 'text-decoration-none' : 'text-decoration-line-through' }'>${ data.query_result[0][i].applyFor[j].name } - ${ data.query_result[0][i].applyFor[j].edition } edition</a>`;
                                          }
                                          trElem.append($(
                                                `<td class='align-middle col-5'>
                                                      <div class='d-flex flex-column book-list'>
                                                      ${ array }
                                                      </div>
                                                </td>`
                                          ));
                                    }

                                    if (status)
                                          trElem.append($(`<td class='align-middle ${ data.query_result[0][i].status === 0 ? 'text-danger' : (data.query_result[0][i].status === 1 ? 'text-success' : 'text-secondary') }'>${ data.query_result[0][i].status === 0 ? 'Ended' : (data.query_result[0][i].status === 1 ? 'On Going' : 'Up Coming') }</td>`));

                                    trElem.append(
                                          $(`<td class='align-middle col-1'>
                                                <div class='d-flex flex-lg-row flex-column'>
                                                      <button onclick='openUpdateModal("${ data.query_result[0][i].id }")' class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
                                                            <i class=\"bi bi-pencil text-white\"></i>
                                                      </button>
                                                      <button onclick='${ status ? 'openDeactivateModal' : 'openActivateModal' }("${ data.query_result[0][i].id }")' class='btn ${ status ? 'btn-danger' : 'btn-success' } ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactive' : 'Activate' }\">
                                                            <i class=\"bi bi-power text-white\"></i>
                                                      </button>
                                                      ${ data.query_result[0][i].deletable ?
                                                      `<button onclick='openDeleteModal("${ data.query_result[0][i].id }")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\">
                                                            <i class=\"bi bi-trash text-white\"></i>
                                                      </button>`: '' }
                                                </div >
                                          </td >`)
                                    );
                              }
                              else if (type === 2)
                              {
                                    trElem.append(
                                          $(`
                                          <td class='align-middle'>${ data.query_result[0][i].name }</td>
                                          <td class='align-middle'>${ data.query_result[0][i].discount }%</td>
                                          <td class='align-middle'>${ data.query_result[0][i].point }</td>
                                          <td class='align-middle col-1'>
                                                <div class='d-flex flex-lg-row flex-column'>
                                                      <button onclick='openUpdateModal("${ data.query_result[0][i].id }")' class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
                                                            <i class=\"bi bi-pencil text-white\"></i>
                                                      </button>
                                                      <button onclick='${ status ? 'openDeactivateModal' : 'openActivateModal' }("${ data.query_result[0][i].id }")' class='btn ${ status ? 'btn-danger' : 'btn-success' } ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactivate' : 'Activate' }\">
                                                            <i class=\"bi bi-power text-white\"></i>
                                                      </button>
                                                      ${ data.query_result[0][i].deletable ?
                                                      `<button onclick='openDeleteModal("${ data.query_result[0][i].id }")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\">
                                                            <i class=\"bi bi-trash text-white\"></i>
                                                      </button>`: '' }
                                                </div>
                                          </td>
                                          `)
                                    );
                              }
                              else if (type === 3)
                              {
                                    trElem.append(
                                          $(`
                                          <td class='align-middle'>${ data.query_result[0][i].name }</td>
                                          <td class='align-middle'>${ data.query_result[0][i].discount }%</td>
                                          <td class='align-middle'>${ data.query_result[0][i].numberOfPeople }</td>
                                          <td class='align-middle col-1'>
                                                <div class='d-flex flex-lg-row flex-column'>
                                                      <button onclick='openUpdateModal("${ data.query_result[0][i].id }")' class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
                                                            <i class=\"bi bi-pencil text-white\"></i>
                                                      </button>
                                                      <button onclick='${ status ? 'openDeactivateModal' : 'openActivateModal' }("${ data.query_result[0][i].id }")' class='btn ${ status ? 'btn-danger' : 'btn-success' } ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"${ status ? 'Deactivate' : 'Activate' }\">
                                                            <i class=\"bi bi-power text-white\"></i>
                                                      </button>
                                                      ${ data.query_result[0][i].deletable ?
                                                      `<button onclick='openDeleteModal("${ data.query_result[0][i].id }")' class='btn btn-danger ms-lg-2 mt-2 mt-lg-0 btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Delete\">
                                                            <i class=\"bi bi-trash text-white\"></i>
                                                      </button>`: '' }
                                                </div >
                                          </td >
                                    `)
                                    );
                              }

                              $('#table_body').append(trElem);

                              initToolTip();
                        }


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
      fetchCouponList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      fetchCouponList();
}