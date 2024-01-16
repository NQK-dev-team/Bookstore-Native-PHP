let originalImg, originalName, originalEdition,
      originalISBN, originalAge, originalPublisher,
      originalPublisherLink, originalPublishDate, originalPhysicalPrice,
      originalFilePrice, originalPhysicalInStock;

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

}

function confirmSubmitForm(e)
{
      e.preventDefault();
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