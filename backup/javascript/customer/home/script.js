let hover_id = null, current_publisher = null, current_category = null;

$(document).ready(function ()
{
      $('#slideRight').get(0).onclick = function ()
      {
            document.getElementById('saleList').scrollLeft += 300;
      };
      $('#slideLeft').get(0).onclick = function ()
      {
            document.getElementById('saleList').scrollLeft -= 300;
      };

      $('#slideRight1').get(0).onclick = function ()
      {
            document.getElementById('bookList').scrollLeft += 300;
      };
      $('#slideLeft1').get(0).onclick = function ()
      {
            document.getElementById('bookList').scrollLeft -= 300;
      };

      $('#slideRight2').get(0).onclick = function ()
      {
            document.getElementById('bookList1').scrollLeft += 300;
      };
      $('#slideLeft2').get(0).onclick = function ()
      {
            document.getElementById('bookList1').scrollLeft -= 300;
      };

      getSales();

      getBestSeller();

      getCategories();

      getPublishers();

      window.addEventListener('resize', function ()
      {
            checkOverflow(0);
            checkOverflow(1);
            checkOverflow(2);
      });
});

function checkOverflow(mode)
{
      if (mode === 0)
      {
            const el = document.getElementById('saleList');
            const curOverflow = el.style.overflow;

            if (!curOverflow || curOverflow === "visible")
                  el.style.overflow = "hidden";

            const isOverflowing = el.clientWidth < el.scrollWidth
                  || el.clientHeight < el.scrollHeight;

            el.style.overflow = curOverflow;

            if (isOverflowing)
            {
                  if (window.innerWidth >= 768)
                        $('#slideNavigate').css('display', 'flex');
                  else
                        $('#slideNavigate').css('display', 'none');
            }
            else
            {
                  $('#slideNavigate').css('display', 'none');
            }
      }
      else if (mode === 1)
      {
            const el = document.getElementById('bookList');
            const curOverflow = el.style.overflow;

            if (!curOverflow || curOverflow === "visible")
                  el.style.overflow = "hidden";

            const isOverflowing = el.clientWidth < el.scrollWidth
                  || el.clientHeight < el.scrollHeight;

            el.style.overflow = curOverflow;

            if (isOverflowing)
            {
                  if (window.innerWidth >= 768)
                        $('#slideNavigate1').css('display', 'flex');
                  else
                        $('#slideNavigate1').css('display', 'none');
            }
            else
            {
                  $('#slideNavigate1').css('display', 'none');
            }
      }
      else if (mode === 2)
      {
            const el = document.getElementById('bookList1');
            const curOverflow = el.style.overflow;

            if (!curOverflow || curOverflow === "visible")
                  el.style.overflow = "hidden";

            const isOverflowing = el.clientWidth < el.scrollWidth
                  || el.clientHeight < el.scrollHeight;

            el.style.overflow = curOverflow;

            if (isOverflowing)
            {
                  if (window.innerWidth >= 768)
                        $('#slideNavigate2').css('display', 'flex');
                  else
                        $('#slideNavigate2').css('display', 'none');
            }
            else
            {
                  $('#slideNavigate2').css('display', 'none');
            }
      }
}

function getSales()
{
      $.ajax({
            url: '/ajax_service/customer/home/get_sales',
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
                        if (data.query_result.length === 0)
                        {
                              $('#salePanel').css('display', 'none');
                              return;
                        }

                        let temp = '';
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              if (i === 0)
                                    temp += `<a class='bg-white card p-2 text-decoration-none me-sm-3 me-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image'>
                                                <div class="card-body p-1 pt-2">
                                                      <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                      <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                      <p class="card-text">Author: ${ data.query_result[i].author.join(', ') } edition</p>
                                                      <span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                </div>
                                          </a>`;
                              else if (i === data.query_result.length - 1)
                                    temp += `<a class='bg-white card p-2 text-decoration-none ms-sm-3 ms-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image'>
                                                <div class="card-body p-1 pt-2">
                                                      <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                      <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                      <p class="card-text">Author: ${ data.query_result[i].author.join(', ') } edition</p>
                                                      <span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                </div>
                                          </a>`;
                              else
                                    temp += `<a class='bg-white card p-2 text-decoration-none mx-sm-3 mx-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image'>
                                                <div class="card-body p-1 pt-2">
                                                      <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                      <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                      <p class="card-text">Author: ${ data.query_result[i].author.join(', ') } edition</p>
                                                      <span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                </div>
                                          </a>`;
                        }
                        $('#saleList').append(temp);
                        checkOverflow(0);

                        // updateTimer(data.query_result[i].endDate);

                        // setInterval(function ()
                        // {
                        //       updateTimer(data.query_result[i].endDate);
                        // }, 1000);
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

// function updateTimer(endDate)
// {
//       const currentTime = new Date();
//       const targetDate = new Date(endDate);
//       const difference = targetDate - currentTime;

//       const days = Math.floor(difference / (1000 * 60 * 60 * 24));
//       const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
//       const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
//       const seconds = Math.floor((difference % (1000 * 60)) / 1000);

//       document.getElementById("days").innerText = days;
//       document.getElementById("hours").innerText = hours;
//       document.getElementById("minutes").innerText = minutes;
//       document.getElementById("seconds").innerText = seconds < 10 ? '0' + seconds : seconds;
// }

function getBestSeller()
{
      $.ajax({
            url: '/ajax_service/customer/home/get_best_sellers',
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
                        if (data.query_result.length === 0)
                              $('#bestSellerPanel').css('display', 'none');

                        let temp1 = '', temp2 = '';
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              temp1 += `<a onmouseover="getBookInfo(this)" data-id='${ data.query_result[i].id }' href='/book/book-detail?id=${ data.query_result[i].id }' class='my-4 d-flex flex-column flex-sm-row pointer best-seller text-dark text-decoration-none'>
                                                      <img alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image' src="${ data.query_result[i].imagePath }" class="best-seller-img mx-auto mx-sm-0">
                                                      <div class='mx-auto ms-sm-3 me-sm-0 d-flex flex-column align-items-center align-items-sm-start'>
                                                            <h5 class='mb-2 mt-2 mt-sm-0 t text-sm-start text-center'>${ data.query_result[i].name }</h5>
                                                            <p class='mb-2'>${ data.query_result[i].edition } edition</p>
                                                            <p class='mb-2'>${ data.query_result[i].author.join(', ') }</p>
                                                            <div class='d-flex'>
                                                            <div class='text-warning'>
                                                            ${ displayRatingStars(data.query_result[i].avgRating) }
                                                            </div>
                                                            <p class='mb-0 ms-2'>(${ data.query_result[i].avgRating })</p>
                                                            </div>
                                                      </div>
                                                </a>`;

                              if (i === 0)
                                    temp2 += `<div id='bestSellerDetail_${ data.query_result[i].id }' name='bestSellerDetail'>
                                                      <div class='d-flex'>
                                                            <img alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image' src="${ data.query_result[i].imagePath }" class="best-seller-img-detail">
                                                            <div class='ms-3'>
                                                                  <h3>${ data.query_result[i].name }</h3>
                                                                  <p>${ data.query_result[i].edition } edition</p>
                                                                  <p>ISBN-13: ${ data.query_result[i].isbn }</p>
                                                                  <p>Author: ${ data.query_result[i].author.join(', ') }</p>
                                                                  <p>Category: ${ data.query_result[i].category.join(', ') }</p>
                                                                  <p>Publisher: ${ data.query_result[i].publisher }</p>
                                                                  <p>Publish Date: ${ data.query_result[i].publishDate }</p>
                                                                  <div class='mb-3 d-flex'>
                                                                  <div class='text-warning'>
                                                                  ${ displayRatingStars(data.query_result[i].avgRating) }
                                                                  </div>
                                                                  <p class='mb-0 ms-2'>(${ data.query_result[i].avgRating })</p>
                                                                  </div>
                                                                  ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                                  <div class='d-flex mt-3'>
                                                                        <p class='text-nowrap'>Hardcover:</p>
                                                                        <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                                        ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                                  </div>
                                                                  <div class='d-flex'>
                                                                        <p class='text-nowrap'>E-book:</p>
                                                                        <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                                        ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <h5 class='mt-3'>Description</h5>
                                                      <p>${ data.query_result[i].description }</p>
                                                </div>`;
                              else
                                    temp2 += `<div class='none' id='bestSellerDetail_${ data.query_result[i].id }' name='bestSellerDetail'>
                                                      <div class='d-flex'>
                                                            <img alt='${ data.query_result[i].name } ${ data.query_result[i].edition } edition image' src="${ data.query_result[i].imagePath }" class="best-seller-img-detail">
                                                            <div class='ms-3'>
                                                                  <h3>${ data.query_result[i].name }</h3>
                                                                  <p>${ data.query_result[i].edition } edition</p>
                                                                  <p>ISBN-13: ${ data.query_result[i].isbn }</p>
                                                                  <p>Author: ${ data.query_result[i].author.join(', ') }</p>
                                                                  <p>Category: ${ data.query_result[i].category.join(', ') }</p>
                                                                  <p>Publisher: ${ data.query_result[i].publisher }</p>
                                                                  <p>Publish Date: ${ data.query_result[i].publishDate }</p>
                                                                  <div class='mb-3 d-flex'>
                                                                  <div class='text-warning'>
                                                                  ${ displayRatingStars(data.query_result[i].avgRating) }
                                                                  </div>
                                                                  <p class='mb-0 ms-2'>(${ data.query_result[i].avgRating })</p>
                                                                  </div>
                                                                  ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                                  <div class='d-flex mt-3'>
                                                                        <p class='text-nowrap'>Hardcover:</p>
                                                                        <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                                        ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                                  </div>
                                                                  <div class='d-flex'>
                                                                        <p class='text-nowrap'>E-book:</p>
                                                                        <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                                        ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <h5 class='mt-3'>Description</h5>
                                                      <p>${ data.query_result[i].description }</p>
                                                </div>`;
                        }

                        $('#bestSellerList1').append(temp1);
                        $('#bestSellerList2').append(temp2);
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

function getBookInfo(elem)
{
      if (hover_id !== $(elem).data('id'))
      {
            hover_id = $(elem).data('id');
            $('div[name="bestSellerDetail"]').each(function ()
            {
                  $(this).css('display', 'none');
            });

            $('#bestSellerDetail_' + hover_id).css('display', 'block');
      }
}

function getPublishers()
{
      $.ajax({
            url: '/ajax_service/customer/home/get_top_publishers',
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
                        if (data.query_result.length === 0)
                        {
                              $('#topPublisherPanel').css('display', 'none');
                              $('#publisherList').empty();
                              return;
                        }

                        let temp = '';
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              if (i === 0)
                                    temp += `<p onclick='switchPublisherBook(this)' class='mb-0 pointer ms-3 me-3 publisher-hover publisher-active text-nowrap' name='publisher' data-name="${ data.query_result[i].publisher }">${ data.query_result[i].publisher }</p>`;
                              else if (i === data.query_result.length - 1)
                                    temp += `<p onclick='switchPublisherBook(this)' class='mb-0 pointer ms-3 publisher-hover text-nowrap' name='publisher' data-name="${ data.query_result[i].publisher }">${ data.query_result[i].publisher }</p>`;
                              else
                                    temp += `<p onclick='switchPublisherBook(this)' class='mb-0 pointer mx-3 publisher-hover text-nowrap' name='publisher' data-name="${ data.query_result[i].publisher }">${ data.query_result[i].publisher }</p>`;
                        }
                        $('#publisherList').append(temp);

                        current_publisher = data.query_result[0].publisher;
                        getPublisherBook(data.query_result[0].publisher);
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

function switchPublisherBook(elem)
{
      if (current_publisher !== $(elem).data('name'))
      {
            current_publisher = $(elem).data('name');
            $('p[name="publisher"]').each(function ()
            {
                  $(this).removeClass('publisher-active');
            });

            $(elem).addClass('publisher-active');

            getPublisherBook(current_publisher);
      }
}

function getPublisherBook(name)
{
      $.ajax({
            url: '/ajax_service/customer/home/get_publisher_book',
            method: 'GET',
            dataType: 'json',
            data: { name: encodeData(name) },
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        let temp = '';
                        $('#bookList').empty();
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              if (i === 0)
                                    temp += `<a class='bg-white card p-1 text-decoration-none me-sm-3 me-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                              else if (i === data.query_result.length - 1)
                                    temp += `<a class='bg-white card p-1 text-decoration-none ms-sm-3 ms-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                              else
                                    temp += `<a class='bg-white card p-1 text-decoration-none mx-sm-3 mx-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                        }
                        $('#bookList').append(temp);
                        checkOverflow(1);
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

function getCategories()
{
      $.ajax({
            url: '/ajax_service/customer/home/get_top_categories',
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
                        if (data.query_result.length === 0)
                        {
                              $('#topCategoryPanel').css('display', 'none');
                              $('#categoryList').empty();
                              return;
                        }

                        let temp = '';
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              if (i === 0)
                                    temp += `<p onclick='switchCategoryBook(this)' class='mb-0 pointer ms-3 me-3 publisher-hover publisher-active text-nowrap' name='category' data-name="${ data.query_result[i].name }">${ data.query_result[i].name }</p>`;
                              else if (i === data.query_result.length - 1)
                                    temp += `<p onclick='switchCategoryBook(this)' class='mb-0 pointer ms-3 publisher-hover text-nowrap' name='category' data-name="${ data.query_result[i].name }">${ data.query_result[i].name }</p>`;
                              else
                                    temp += `<p onclick='switchCategoryBook(this)' class='mb-0 pointer mx-3 publisher-hover text-nowrap' name='category' data-name="${ data.query_result[i].name }">${ data.query_result[i].name }</p>`;
                        }
                        $('#categoryList').append(temp);

                        current_category = data.query_result[0].name;
                        getCategoryBook(data.query_result[0].name);
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

function switchCategoryBook(elem)
{
      if (current_category !== $(elem).data('name'))
      {
            current_category = $(elem).data('name');
            $('p[name="category"]').each(function ()
            {
                  $(this).removeClass('publisher-active');
            });

            $(elem).addClass('publisher-active');

            getCategoryBook(current_category);
      }
}

function getCategoryBook(name)
{
      $.ajax({
            url: '/ajax_service/customer/home/get_category_book',
            method: 'GET',
            dataType: 'json',
            data: { name: encodeData(name) },
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        let temp = '';
                        $('#bookList1').empty();
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              if (i === 0)
                                    temp += `<a class='bg-white card p-1 text-decoration-none me-sm-3 me-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                              else if (i === data.query_result.length - 1)
                                    temp += `<a class='bg-white card p-1 text-decoration-none ms-sm-3 ms-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                              else
                                    temp += `<a class='bg-white card p-1 text-decoration-none mx-sm-3 mx-2' href='/book/book-detail?id=${ data.query_result[i].id }'>
                                                      <img class="card-img-top" src="${ data.query_result[i].imagePath }" alt="${ data.query_result[i].name } ${ data.query_result[i].edition } edition image">
                                                      <div class="card-body p-1 pt-2">
                                                            <h5 class="card-title">${ data.query_result[i].name }</h5>
                                                            <p class="card-text">${ data.query_result[i].edition } edition</p>
                                                            <p class="card-text">Author: ${ data.query_result[i].author.join(', ') }</p>
                                                            ${ data.query_result[i].discount ? `<span class='bg-danger p-1 rounded text-white'>-${ data.query_result[i].discount }%</span>` : '' }
                                                      <div class='d-flex mt-3'>
                                                            <p class='text-nowrap'>Hardcover:</p>
                                                            <p class='${ data.query_result[i].physicalPrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].physicalPrice ? '$' + data.query_result[i].physicalPrice : 'N/A' }</p>
                                                            ${ data.query_result[i].physicalPrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].physicalPrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                     <div class='d-flex'>
                                                            <p class='text-nowrap'>E-book:</p>
                                                            <p class='${ data.query_result[i].filePrice && data.query_result[i].discount ? 'text-decoration-line-through' : '' } mx-2 fw-medium'>${ data.query_result[i].filePrice ? '$' + data.query_result[i].filePrice : 'N/A' }</p>
                                                            ${ data.query_result[i].filePrice && data.query_result[i].discount ? `<p class='fw-medium'>$${ (data.query_result[i].filePrice * (100.0 - data.query_result[i].discount) / 100).toFixed(2) }</p>` : '' }
                                                      </div>
                                                      </div>
                                                </a>`;
                        }
                        $('#bookList1').append(temp);
                        checkOverflow(2);
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

function viewMorePublisherBook()
{
      window.location.href = `/book/?publisher=${ encodeData(current_publisher) }`;
}

function viewMoreCategoryBook()
{
      window.location.href = `/book/?category=${ encodeData(current_category) }`;
}