let update_id = null, delete_id = null, activate_id = null, deactivate_id = null, bookApply = [], selectAll = [];

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

      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            delete_id = null;
      });

      $('#deactivateModal').on('hidden.bs.modal', function ()
      {
            deactivate_id = null;
      });

      $('#activateModal').on('hidden.bs.modal', function ()
      {
            activate_id = null;
      });

      $('#addCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmAddModal').modal('show');
      });

      $('#updateCouponForm').submit(function (event)
      {
            event.preventDefault();
            $('#confirmUpdateModal').modal('show');
      });

      $('#addModal').on('hidden.bs.modal', function ()
      {
            $('#addCouponForm').empty();
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

      initToolTip();
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
            $('#error_message').text('Selected `Coupon Status` data type invalid!');
            return;
      }

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      const nextBtnDisabledProp = $('#next_button').prop('disabled');
      const prevBtnDisabledProp = $('#prev_button').prop('disabled');

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/admin/discount/retrieve_list.php',
            method: 'GET',
            data: { entry: entry, offset: listOffset, search: search, type: type, status: status },
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
                                                      <div class='d-flex flex-column'>
                                                      ${ array }
                                                      </div>
                                                </td>`
                                          ));
                                    }

                                    trElem.append(
                                          $(`<td class='align-middle col-1'>
                                                <div class='d-flex flex-lg-row flex-column'>
                                                      <button class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
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
                                                      <button class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
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
                                                      <button class='btn btn-info btn-sm' data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" data-bs-title=\"Edit\">
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
      fetchCouponList();
}

function selectEntry()
{
      $('#list_offset').text(1);
      $('#prev_button').attr('disabled', true);
      $('#next_button').attr('disabled', false);
      fetchCouponList();
}

function openDeleteModal(id)
{
      delete_id = id;
      $('#deleteModal').modal('show');
}

function deleteCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/discount/delete_discount.php',
            type: 'DELETE',
            data: {
                  id: encodeData(delete_id),
                  type: encodeData(type)
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
                  fetchCouponList();
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

function openActivateModal(id)
{
      activate_id = id;
      $('#activateModal').modal('show');
}

function activateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/discount/update_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(activate_id),
                  status: true,
                  type: encodeData(type)
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
                  fetchCouponList();
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

function openDeactivateModal(id)
{
      deactivate_id = id;
      $('#deactivateModal').modal('show');
}

function deactivateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      $.ajax({
            url: '/ajax_service/admin/discount/update_status.php',
            type: 'PATCH',
            data: {
                  id: encodeData(deactivate_id),
                  status: false,
                  type: encodeData(type)
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
                  fetchCouponList();
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

function openAddModal()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponStartDate" class="form-label">Start Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="date" class="form-control" id="couponStartDate">
                  </div>
                  <div class='mt-2'>
                        <label for="couponEndDate" class="form-label">End Date:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="date" class="form-control" id="couponEndDate">
                  </div>
                  <div class='mt-2'>
                        <label for="couponBookApply" class="form-label">Books Applied:</label>
                        <input readonly type="text" class="form-control pointer" id="couponBookApply" onclick="chooseBook()">
                  </div>`)
            );
      else if (type === 2)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                              <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPoint" class="form-label">Accumulated Points:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPoint" min="0" placeholder="Enter value">
                  </div>`)
            );
      else if (type === 3)
            $('#addCouponForm').append(
                  $(`<div>
                        <label for="couponName" class="form-label">Coupon Name:<span class="fw-bold text-danger">&nbsp;*</span></label>
                              <input type="text" class="form-control" id="couponName" placeholder="Enter coupon name">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPercentage" class="form-label">Discount Percentage:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPercentage" min="0" placeholder="Enter value">
                  </div>
                  <div class='mt-2'>
                        <label for="couponPeople" class="form-label">Number of People:<span class="fw-bold text-danger">&nbsp;*</span></label>
                        <input type="number" class="form-control" id="couponPeople" min="0" placeholder="Enter value">
                  </div>`)
            );


      $('#addModal').modal('show');
}

function addCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
      {

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}

function openUpdateModal()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
      {

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}

function updateCoupon()
{
      const type = parseInt(encodeData($('#couponSelect').val()));

      if (typeof type !== 'number' || isNaN(type) || type < 0 || type > 3)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Selected `Coupon Type` data type invalid!');
            return;
      }

      if (type === 1)
      {

      }
      else if (type === 2)
      {

      }
      else if (type === 3)
      {

      }
}