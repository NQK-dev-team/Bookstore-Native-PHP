let originalImg, originalName, originalEdition,
      originalISBN, originalAge, originalPublisher,
      originalPublisherLink, originalPublishDate, originalPhysicalPrice,
      originalFilePrice, originalPhysicalInStock, originalAuthor, originalCategory;

$(document).ready(() =>
{
      initToolTip();

      originalImg = $('#bookImage').prop('src');
      originalName = $('#bookNameInput').val();
      originalEdition = $('#editionInput').val();
      originalISBN = $('#isbnInput').val();
      originalAge = $('#ageInput').val();
      originalPublisher = $('#publisherInput').val();
      originalPublisherLink = $('#publisherLinkInput').val();
      originalPublishDate = $('#publishDateInput').val();
      originalPhysicalPrice = $('#physicalPriceInput').val();
      originalFilePrice = $('#filePriceInput').val();
      originalPhysicalInStock = $('#inStockInput').val();
      originalAuthor = $('#authorInput').val();
      originalCategory = $('#categoryInput').val();

      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#categoryModal').on('show.bs.modal', () =>
      {
            getCategory($('#searchCategoryInput').val());
      });
});

window.addEventListener('beforeunload', function (event)
{
      const expr = $('#bookNameInput').val() !== originalName
            || $('#bookImage').attr('src') !== originalImg
            || $('#editionInput').val() !== originalEdition
            || $('#isbnInput').val() !== originalISBN
            || $('#ageInput').val() !== originalAge
            || $('#publisherInput').val() !== originalPublisher
            || $('#publisherLinkInput').val() !== originalPublisherLink
            || $('#publishDateInput').val() !== originalPublishDate
            || $('#physicalPriceInput').val() !== originalPhysicalPrice
            || $('#inStockInput').val() !== originalPhysicalInStock
            || $('#filePriceInput').val() !== originalFilePrice
            || $('#authorInput').val() !== originalAuthor
            || $('#categoryInput').val() !== originalCategory
            || $('#pdfFileName').text() !== '';

      if (expr)
            event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
});

function resetForm()
{
      $('#bookNameInput').val(originalName);

      $('#bookImage').attr('src', originalImg);
      $('#imageFileName').text('');

      $('#editionInput').val(originalEdition);

      $('#isbnInput').val(originalISBN);

      $('#ageInput').val(originalAge);

      $('#publisherInput').val(originalPublisher);

      $('#publisherLinkInput').val(originalPublisherLink);

      $('#publishDateInput').val(originalPublishDate);

      $('#physicalPriceInput').val(originalPhysicalPrice);

      $('#inStockInput').val(originalPhysicalInStock);

      $('#filePriceInput').val(originalFilePrice);

      const newFileInput = $(`<input type="file" class="form-control d-none" id="filePathInput" accept='.pdf' onchange="setNewFile(event)">`);
      $('#filePathInput').replaceWith(newFileInput);
      $('#pdfFileName').text('');

      $('#authorInput').val(originalAuthor);

      $('#categoryInput').val(originalCategory);
}

function openCategoryModal()
{
      $('#categoryModal').modal('show');
}

function getCategory(search)
{
      $.ajax({
            url: '/ajax_service/book/get_category_list.php',
            method: 'GET',
            data: { search: sanitize(search) },
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
                        for (let i = 0; i < data.query_result.length - 1; i += 2)
                        {
                              $('#category_list').append(
                                    $(`
                                    <div class='d-flex flex-sm-row flex-column w-100'>
                                          <div class="form-check mx-auto check_box">
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[i].name }" id="category_${ i + 1 }" ${ $('#categoryInput').val().includes(data.query_result[i].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ i + 1 }">
                                                      ${ data.query_result[i].name }
                                                </label>
                                                <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i].description }"></i>
                                          </div>
                                          <div class="form-check mx-auto check_box">
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[i + 1].name }" id="category_${ i + 2 }" ${ $('#categoryInput').val().includes(data.query_result[i + 1].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ i + 2 }">
                                                      ${ data.query_result[i + 1].name }
                                                </label>
                                                <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i + 1].description }"></i>
                                          </div>
                                    </div>
                                    `)
                              );
                        }
                        if (data.query_result.length % 2)
                        {
                              $('#category_list').append(
                                    $(`
                                    <div class='d-flex flex-sm-row flex-column w-100'>
                                          <div class="form-check mx-auto check_box">
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[data.query_result.length - 1].name }" id="category_${ data.query_result.length }" ${ $('#categoryInput').val().includes(data.query_result[data.query_result.length - 1].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ data.query_result.length }">
                                                      ${ data.query_result[data.query_result.length - 1].name }
                                                </label>
                                                <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[data.query_result.length - 1].description }"></i>
                                          </div>
                                          <div class='mx-auto check_box' aria-label="Dummy Element">
                                          </div>
                                    </div>
                                    `)
                              );
                        }

                        initToolTip();
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

function setCategory(e)
{
      const arr = $('#categoryInput').val().split(', ');
      if (e.target.checked)
      {
            if (!arr.includes(e.target.value))
            {
                  arr.push(e.target.value);
                  $('#categoryInput').val(arr.join(', '));
            }
      }
      else
      {
            if (arr.includes(e.target.value))
            {
                  arr.splice(arr.indexOf(e.target.value), 1);
                  $('#categoryInput').val(arr.join(', '));
            }

      }
}

function confirmSubmitForm(e)
{
      e.preventDefault();
      $('#confirmModal').modal('show');
}

function submitForm()
{
}

function setNewImage(e)
{
      const file = e.target.files;
      $('#imageFileName').text(file.length === 1 ? file[0].name : '');

      if (file.length === 1)
      {
            const reader = new FileReader();

            reader.onload = function (e)
            {
                  $('#bookImage').attr('src', e.target.result);
            };

            reader.readAsDataURL(file[0]);
      }
      else
            $('#bookImage').attr('src', originalImg);
}

function setNewFile(e)
{
      const file = e.target.files;
      $('#pdfFileName').text(file.length === 1 ? file[0].name : '');
}

$("#category_search_form").submit(function (e)
{
      e.preventDefault();
      getCategory($('#searchCategoryInput').val());
});
