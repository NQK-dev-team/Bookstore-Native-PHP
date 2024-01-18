let originalImg = null, originalName = null, originalEdition = null,
      originalISBN = null, originalAge = null, originalPublisher = null,
      originalPublishDate = null, originalPhysicalPrice = null, originalFilePrice = null,
      originalPhysicalInStock = null, originalAuthor = null, originalCategory = null, originalDescription = null;

let newImg = null, newFile = null;

$(document).ready(() =>
{
      initToolTip();

      originalImg = $('#bookImage').prop('src');
      originalName = $('#bookNameInput').val();
      originalEdition = $('#editionInput').val();
      originalISBN = $('#isbnInput').val();
      originalAge = $('#ageInput').val();
      originalPublisher = $('#publisherInput').val();
      originalPublishDate = $('#publishDateInput').val();
      originalPhysicalPrice = $('#physicalPriceInput').val();
      originalFilePrice = $('#filePriceInput').val();
      originalPhysicalInStock = $('#inStockInput').val();
      originalAuthor = $('#authorInput').val();
      originalCategory = $('#categoryInput').val();
      originalDescription = $('#descriptionInput').val();

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
            || $('#publishDateInput').val() !== originalPublishDate
            || $('#physicalPriceInput').val() !== originalPhysicalPrice
            || $('#inStockInput').val() !== originalPhysicalInStock
            || $('#filePriceInput').val() !== originalFilePrice
            || $('#authorInput').val() !== originalAuthor
            || $('#categoryInput').val() !== originalCategory
            || $('#pdfFileName').text() !== ''
            || newImage || newFile;

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

      $('#publishDateInput').val(originalPublishDate);

      $('#physicalPriceInput').val(originalPhysicalPrice);

      $('#inStockInput').val(originalPhysicalInStock);

      $('#filePriceInput').val(originalFilePrice);

      const newFileInput = $(`<input type="file" class="form-control d-none" id="filePathInput" accept='.pdf' onchange="setNewFile(event)">`);
      $('#filePathInput').replaceWith(newFileInput);
      $('#pdfFileName').text('');

      $('#authorInput').val(originalAuthor);

      $('#categoryInput').val(originalCategory);

      $('#descriptionInput').val(originalDescription);

      newImg = null;
      newFile = null;
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
                                                      <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i].description }"></i>
                                                </label>
                                          </div>
                                          <div class="form-check mx-auto check_box">
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[i + 1].name }" id="category_${ i + 2 }" ${ $('#categoryInput').val().includes(data.query_result[i + 1].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ i + 2 }">
                                                      ${ data.query_result[i + 1].name }
                                                      <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i + 1].description }"></i>
                                                </label>
                                          </div>
                                    </div>
                                    `)
                              );
                        }
                        if (data.query_result.length && data.query_result.length % 2)
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
                  arr.sort();
                  $('#categoryInput').val(arr.join(', '));
            }
      }
      else
      {
            if (arr.includes(e.target.value))
            {
                  arr.splice(arr.indexOf(e.target.value), 1);
                  arr.sort();
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
      const name = sanitize($('#bookNameInput').val());
      const edition = sanitize($('#editionInput').val()) === '' ? '' : parseInt(sanitize($('#editionInput').val()));
      const isbn = sanitize($('#isbnInput').val().replace(/-/g, ''));
      const age = sanitize($('#ageInput').val()) === '' ? '' : parseInt(sanitize($('#ageInput').val()));
      const author = $('#authorInput').val().split(',').map(str => sanitize(str));
      const category = $('#categoryInput').val().split(',').map(str => sanitize(str));
      const publisher = sanitize($('#publisherInput').val());
      const publishDate = sanitize($('#publishDateInput').val());
      const physicalPrice = sanitize($('#physicalPriceInput').val()) === '' ? '' : parseFloat(sanitize($('#physicalPriceInput').val()));
      const inStock = sanitize($('#inStockInput').val()) === '' ? '' : parseInt(sanitize($('#inStockInput').val()));
      const filePrice = sanitize($('#filePriceInput').val()) === '' ? '' : parseFloat(sanitize($('#filePriceInput').val()));
      const description = sanitize($('#descriptionInput').val());

      console.log(name);
      console.log(edition);
      console.log(isbn);
      console.log(age);
      console.log(author);
      console.log(category);
      console.log(publisher);
      console.log(publishDate);
      console.log(physicalPrice);
      console.log(inStock);
      console.log(filePrice);
      console.log(description);

      if (name === '')
      {
            reportCustomValidity($('#bookNameInput').get(0), 'Book name is empty!');
            return;
      }

      if (typeof edition === 'string' && edition === '')
      {
            reportCustomValidity($('#editionInput').get(0), 'Book edition is empty!');
            return;
      }
      else if (!(typeof edition === 'number' && !isNaN(edition) && edition > 0))
      {
            reportCustomValidity($('#editionInput').get(0), 'Book edition invalid!');
            return;
      }

      if (isbn === '')
      {
            reportCustomValidity($('#isbnInput').get(0), 'Book ISBN-13 is empty!');
            return;
      }
      else
      {
            const regex = /^[0-9]{13}$/;
            if (!regex.test(isbn))
            {
                  reportCustomValidity($('#isbnInput').get(0), 'Book ISBN-13 invalid!');
                  return;
            }
      }

      if (!((typeof age === 'number' && !isNaN(age) && age > 0) || (typeof age === 'string' && age === '')))
      {
            reportCustomValidity($('#ageInput').get(0), 'Age restriction invalid!');
            return;
      }

      if (author.length === 0)
      {
            reportCustomValidity($('#authorInput').get(0), 'Book must have at least one author!');
            return;
      }

      if (publisher === '')
      {
            reportCustomValidity($('#publisherInput').get(0), 'Publisher is empty!');
            return;
      }

      if (publishDate === '')
      {
            reportCustomValidity($('#publishDateInput').get(0), 'Publish date is empty!');
            return;
      }
      else 
      {
            const localPublishDate = new Date(publishDate);
            const today = new Date();
            if (localPublishDate > today)
            {
                  reportCustomValidity($('#publishDateInput').get(0), 'Publish date invalid!');
                  return;
            }
      }

      if (!((typeof physicalPrice === 'number' && !isNaN(physicalPrice) && physicalPrice > 0) || (typeof physicalPrice === 'string' && physicalPrice === '')))
      {
            reportCustomValidity($('#physicalPriceInput').get(0), 'Physical copy price invalid!');
            return;
      }

      if (!((typeof inStock === 'number' && !isNaN(inStock) && inStock >= 0) || (typeof inStock === 'string' && inStock === '')))
      {
            reportCustomValidity($('#inStockInput').get(0), 'Physical copy in stock invalid!');
            return;
      }

      if (!((typeof filePrice === 'number' && !isNaN(filePrice) && filePrice > 0) || (typeof filePrice === 'string' && filePrice === '')))
      {
            reportCustomValidity($('#filePriceInput').get(0), 'File copy price invalid!');
            return;
      }

      let isOK = true;

      if (newImg && newImg.type !== 'image/jpeg' && newImg.type !== 'image/png')
      {
            $('#imgeFileErrorMessage').text('Invalid image file!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            isOK = false;
      }
      else if (newImg && newImg.size > 5 * 1024 * 1024)
      {
            $('#imgeFileErrorMessage').text('Image size too large!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            isOK = false;
      }
      else
      {
            $('#imgeFileErrorMessage').text('');
            $('#imgeFileError').addClass('d-none').removeClass('d-flex');
      }

      if (newFile && newFile.type !== 'application/pdf')
      {
            $('#pdfFileError').removeClass('d-none');
            isOK = false;
      }
      else
            $('#pdfFileError').addClass('d-none');

      if (!isOK) return;

      const postData = new FormData();
      postData.append('name', name);
      postData.append('edition', edition);
      postData.append('isbn', isbn);
      postData.append('age', age);
      postData.append('author', author);
      postData.append('category', category);
      postData.append('publisher', publisher);
      postData.append('publishDate', publishDate);
      postData.append('description', description);
      postData.append('physicalPrice', physicalPrice);
      postData.append('filePrice', filePrice);
      postData.append('inStock', inStock);
      postData.append('image', newImg);
      postData.append('pdf', newFile);

      $('*').addClass('wait');
      $('button, input').prop('disabled', true);
      $('a').addClass('disable_link');

      $.ajax({
            url: '/ajax_service/book/update_book.php',
            method: 'POST',
            data: postData,
            contentType: false,
            processData: false,
            //dataType: 'json',
            success: function (data)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {

                        initToolTip();
                  }
            },

            error: function (err)
            {
                  $('*').removeClass('wait');
                  $('button, input').prop('disabled', false);
                  $('a').removeClass('disable_link');

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

function setNewImage(e)
{
      const file = e.target.files;
      $('#imageFileName').text(file.length === 1 ? file[0].name : '');
      newImg = file.length === 1 ? file[0] : null;

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
      newFile = file.length === 1 ? file[0] : null;
}

$("#category_search_form").submit(function (e)
{
      e.preventDefault();
      getCategory($('#searchCategoryInput').val());
});
