$(document).ready(function ()
{
      $.ajax({
            url: '/ajax_service/admin/home/get_best_book.php',
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
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              $(`#book_image_${ i + 1 }`).attr('src', data.query_result[i].imagePath);
                              $(`#book_name_${ i + 1 }`).text(data.query_result[i].name);
                              $(`#book_edition_${ i + 1 }`).text(data.query_result[i].edition);
                              $(`#book_isbn_${ i + 1 }`).text(data.query_result[i].isbn);
                              $(`#book_age_${ i + 1 }`).text(data.query_result[i].ageRestriction);
                              $(`#book_sold_${ i + 1 }`).text(data.query_result[i].finalTotalSold === 1 ? data.query_result[i].finalTotalSold + ' copy' : data.query_result[i].finalTotalSold + ' copies');
                              $(`#book_publisher_${ i + 1 }`).text(data.query_result[i].publisher + ' (' + data.query_result[i].publishDate + ')');
                              $(`#book_category_${ i + 1 }`).text(data.query_result[i].category.length ? data.query_result[i].category.join(', ') : 'N/A');
                              $(`#book_author_${ i + 1 }`).text(data.query_result[i].author.join(', '));
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