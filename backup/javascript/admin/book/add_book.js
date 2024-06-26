let newImg = null, newFile = null, isSuccess = false;

let imageError = false, pdfError = false;

$(document).ready(() =>
{
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#categoryModal').on('show.bs.modal', () =>
      {
            getCategory($('#searchCategoryInput').val());
      });

      $('#successModal').on('hidden.bs.modal', () =>
      {
            window.location.href = '/admin/book/';
      });

      $('#bookNameInput').focus();
});

function resetForm()
{
      $('#bookNameInput').val('');

      $('#bookImage').addClass('d-none');
      $('#bookImage').removeAttr('src');
      $('#bookImagePlaceHolder').removeClass('d-none');
      $('#imageFileName').text('');
      $('#imageInput').val('');

      $('#editionInput').val('');

      $('#isbnInput').val('');

      $('#publisherInput').val('');

      $('#publishDateInput').val('');

      $('#physicalPriceInput').val('');

      $('#inStockInput').val('');

      $('#filePriceInput').val('');

      $('#filePathInput').val('');
      $('#pdfFileName').text('');

      $('#authorInput').val('');

      $('#categoryInput').val('');

      $('#descriptionInput').val('');

      newImg = null;
      newFile = null;

      $('#imgeFileErrorMessage').text('');
      $('#imgeFileError').addClass('d-none').removeClass('d-flex');

      imageError = false;

      $('#pdfFileError1').addClass('d-none');

      $('#pdfFileError2').addClass('d-none');
      pdfError = false;
}

function openCategoryModal()
{
      $('#categoryModal').modal('show');
}

function getCategory(search)
{
      $.ajax({
            url: '/ajax_service/admin/book/get_category_list.php',
            method: 'GET',
            data: { search: encodeData(search) },
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
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[i].name }" id="category_${ i + 1 }" ${ $('#categoryInput').val().split('\n').includes(data.query_result[i].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ i + 1 }">
                                                      ${ data.query_result[i].name }
                                                      <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i].description ? data.query_result[i].description : 'N/A' }"></i>
                                                </label>
                                          </div>
                                          <div class="form-check mx-auto check_box">
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[i + 1].name }" id="category_${ i + 2 }" ${ $('#categoryInput').val().split('\n').includes(data.query_result[i + 1].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ i + 2 }">
                                                      ${ data.query_result[i + 1].name }
                                                      <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[i + 1].description ? data.query_result[i + 1].description : 'N/A' }"></i>
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
                                                <input onchange="setCategory(event)" class="form-check-input pointer" type="checkbox" value="${ data.query_result[data.query_result.length - 1].name }" id="category_${ data.query_result.length }" ${ $('#categoryInput').val().split('\n').includes(data.query_result[data.query_result.length - 1].name) ? 'checked' : '' }>
                                                <label class="form-check-label" for="category_${ data.query_result.length }">
                                                      ${ data.query_result[data.query_result.length - 1].name }
                                                </label>
                                                <i class="bi bi-question-circle help ms-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${ data.query_result[data.query_result.length - 1].description ? data.query_result[data.query_result.length - 1].description : 'N/A' }"></i>
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
      const arr = $('#categoryInput').val() !== '' ? $('#categoryInput').val().split('\n').map(x => x.trim()) : [];
      if (e.target.checked)
      {
            if (!arr.includes(e.target.value))
            {
                  arr.push(e.target.value);
                  arr.sort();
                  $('#categoryInput').val(arr.join('\n'));
            }
      }
      else
      {
            if (arr.includes(e.target.value))
            {
                  arr.splice(arr.indexOf(e.target.value), 1);
                  arr.sort();
                  $('#categoryInput').val(arr.join('\n'));
            }

      }
}

function confirmSubmitForm(e)
{
      e.preventDefault();
      if (!imageError && !pdfError)
            $('#confirmModal').modal('show');
}

function submitForm()
{
      $('#confirmModal').modal('hide');

      const name = encodeData($('#bookNameInput').val());
      const edition = encodeData($('#editionInput').val()) === '' ? '' : parseInt(encodeData($('#editionInput').val()));
      const isbn = encodeData($('#isbnInput').val().replace(/-/g, ''));
      const author = $('#authorInput').val() !== '' ? (($('#authorInput').val().split(',')).filter(str => str.trim() !== '')).map(str => encodeData(str)) : '';
      const category = $('#categoryInput').val() !== '' ? (($('#categoryInput').val().split('\n')).filter(str => str.trim() !== '')).map(str => encodeData(str)) : '';// encodeData($('#categoryInput').val());
      const publisher = encodeData($('#publisherInput').val());
      const publishDate = encodeData($('#publishDateInput').val());
      const physicalPrice = encodeData($('#physicalPriceInput').val()) === '' ? '' : parseFloat(encodeData($('#physicalPriceInput').val()));
      const inStock = encodeData($('#inStockInput').val()) === '' ? '' : parseInt(encodeData($('#inStockInput').val()));
      const filePrice = encodeData($('#filePriceInput').val()) === '' ? '' : parseFloat(encodeData($('#filePriceInput').val()));
      const description = encodeData($('#descriptionInput').val());

      if (name === '')
      {
            reportCustomValidity($('#bookNameInput').get(0), 'Book name is empty!');
            return;
      }
      else
      {
            const regex = /[?/\\]/;
            const localName = name.replace(/%2F/g, '/').replace(/%3F/g, '?').replace(/%5C/g, '\\').replace(/%22/g, '\"');
            if (regex.test(localName))
            {
                  reportCustomValidity($('#bookNameInput').get(0), 'Book name must not contain \'?\', \'/\', \'\"\' or \'\\\' characters!');
                  return;
            }
            else if (name.length > 255)
            {
                  reportCustomValidity($('#bookNameInput').get(0), 'Book name must be at most 255 characters long or less!');
                  return;
            }
      }

      if (!newImg)
      {
            $('#imgeFileErrorMessage').text('Missing image file!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            imageError = true;
            return;
      }
      else
      {
            if (newImg.type !== 'image/jpeg' && newImg.type !== 'image/png')
            {
                  $('#imgeFileErrorMessage').text('Invalid image file!');
                  $('#imgeFileError').removeClass('d-none').addClass('d-flex');
                  imageError = true;
                  return;
            }
            else if (newImg.size > 5 * 1024 * 1024)
            {
                  $('#imgeFileErrorMessage').text('Image size must be 5MB or less!');
                  $('#imgeFileError').removeClass('d-none').addClass('d-flex');
                  imageError = true;
                  return;
            }
            else
            {
                  $('#imgeFileErrorMessage').text('');
                  $('#imgeFileError').addClass('d-none').removeClass('d-flex');
            }
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

      if (author.length === 0)
      {
            reportCustomValidity($('#authorInput').get(0), 'Book must have at least one author!');
            return;
      }
      else if (author.findIndex(elem => elem.length > 255) !== -1)
      {
            reportCustomValidity($('#authorInput').get(0), 'Author name must be at most 255 characters long or less!');
            return;
      }

      if (category.length === 0)
      {
            $('#errorModal').modal('show');
            $('#error_message').text('Book must belong to at least one category!');
            return;
      }


      if (publisher === '')
      {
            reportCustomValidity($('#publisherInput').get(0), 'Publisher is empty!');
            return;
      }
      else if (publisher.length > 255)
      {
            reportCustomValidity($('#publisherInput').get(0), 'Publisher must be at most 255 characters long or less!');
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
            localPublishDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            if (localPublishDate > today)
            {
                  reportCustomValidity($('#publishDateInput').get(0), 'Publish date invalid!');
                  return;
            }
      }

      if (description.length > 2000)
      {
            reportCustomValidity($('#descriptionInput').get(0), 'Description must be at most 2000 characters long or less!');
            return;
      }

      if (!((typeof physicalPrice === 'number' && !isNaN(physicalPrice) && physicalPrice > 0) || (typeof physicalPrice === 'string' && physicalPrice === '')))
      {
            reportCustomValidity($('#physicalPriceInput').get(0), 'Hardcover price invalid!');
            return;
      }

      if (!((typeof inStock === 'number' && !isNaN(inStock) && inStock >= 0) || (typeof inStock === 'string' && inStock === '')))
      {
            reportCustomValidity($('#inStockInput').get(0), 'Hardcover in stock invalid!');
            return;
      }

      if (!((typeof filePrice === 'number' && !isNaN(filePrice) && filePrice > 0) || (typeof filePrice === 'string' && filePrice === '')))
      {
            reportCustomValidity($('#filePriceInput').get(0), 'E-book price invalid!');
            return;
      }

      if (newFile && newFile.type !== 'application/pdf')
      {
            $('#pdfFileError1').removeClass('d-none');
            pdfError = true;
            return;
      }
      else
      {
            $('#pdfFileError1').addClass('d-none');
      }

      const postData = new FormData();
      postData.append('name', name);
      postData.append('edition', edition);
      postData.append('isbn', isbn);
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

      $.ajax({
            url: '/ajax_service/admin/book/add_book.php',
            method: 'POST',
            data: postData,
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
            contentType: false,
            processData: false,
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
                        isSuccess = true;
                        $('#successModal').modal('show');
                        initToolTip();
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
}

function setNewImage(e)
{
      const file = e.target.files;
      if (file.length > 1)
      {
            $('#imgeFileErrorMessage').text('Only submit 1 image file!');
            $('#imgeFileError').removeClass('d-none').addClass('d-flex');
            imageError = true;
            return;
      }
      else
      {
            $('#imgeFileErrorMessage').text('');
            $('#imgeFileError').removeClass('d-flex').addClass('d-none');
            imageError = false;
      }
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

            $('#imgeFileErrorMessage').text('');
            $('#imgeFileError').addClass('d-none').removeClass('d-flex');
            $('#bookImage').removeClass('d-none');
            $('#bookImagePlaceHolder').addClass('d-none');
      }
      else
      {
            $('#bookImage').addClass('d-none');
            $('#bookImage').removeAttr('src');
            $('#bookImagePlaceHolder').removeClass('d-none');
      }
}

function setNewFile(e)
{
      const file = e.target.files;
      if (file.length > 1)
      {
            $('#pdfFileError2').removeClass('d-none');
            pdfError = true;
            return;
      }
      else
      {
            $('#pdfFileError2').addClass('d-none');
            pdfError = false;
      }
      $('#pdfFileName').text(file.length === 1 ? file[0].name : '');
      newFile = file.length === 1 ? file[0] : null;
}

$("#category_search_form").submit(function (e)
{
      e.preventDefault();
      getCategory($('#searchCategoryInput').val());
});
