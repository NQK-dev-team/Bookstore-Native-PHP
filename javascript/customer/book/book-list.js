let categorySelected = '', authorSelected = '', publisherSelected = '';
let displayPanel = null;
let offset = null;
let itemPerRow = null;

$(document).ready(function ()
{
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    categorySelected = urlParams.get('category') || '';
    publisherSelected = urlParams.get('publisher') || '';

    if (urlParams.get('select') === 'discount')
        $('#listOption').val(2);
    else if (urlParams.get('select') === 'sales')
        $('#listOption').val(3);

    $('#categorySearch,#categorySearchModal').val(categorySelected);
    $('#publisherSearch,#publisherSearchModal').val(publisherSelected);

    offset = 1;

    fetchCategoryList();
    fetchAuthorList();
    fetchPublisherList();

    checkScreenWidth();

    $(window).resize(function ()
    {
        checkScreenWidth();
    });

    $('#search_form').submit(function (e)
    {
        e.preventDefault();
        fetchBook();
    });

    $('#newBookForm').submit(function (e)
    {
        e.preventDefault();
        addNewBook();
    });
});

function checkScreenWidth()
{
    if (window.innerWidth < 1200)
        displayPanel = false;
    else
        displayPanel = true;

    const temp = itemPerRow;
    // if (window.innerWidth >= 1200)
    //     itemPerRow = 3;
    // if (window.innerWidth < 1200)
    //     itemPerRow = 4;
    // if (window.innerWidth < 992)
    //     itemPerRow = 3;
    if (window.innerWidth >= 1200)
        itemPerRow = 3;
    if (window.innerWidth >= 1100 && window.innerWidth < 1200)
        itemPerRow = 4;
    if (window.innerWidth >= 850 && window.innerWidth < 1100)
        itemPerRow = 3;
    if (window.innerWidth < 850)
        itemPerRow = 2;
    if (window.innerWidth < 576)
        itemPerRow = 1;
    if (temp !== itemPerRow)
    {
        fetchBook();
    }
}

function fetchCategoryList()
{
    const search = displayPanel ? encodeData($('#categorySearch').val()) : encodeData($('#categorySearchModal').val());

    if (displayPanel)
        $('#categorySearchModal').val($('#categorySearch').val());
    else
        $('#categorySearch').val($('#categorySearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_category.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#categoryList,#categoryListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="chooseCategory(event)" name='category' class='pointer ${ categorySelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#categoryList,#categoryListModal').append(temp);
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

function chooseCategory(e)                          
{
    $('p[name="category"]').removeClass('itemChoose');
    if (categorySelected !== e.target.innerText)
    {
        $(`p[name="category"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
        categorySelected = e.target.innerText;
    } else
        categorySelected = '';
    fetchBook();
}

function fetchAuthorList()
{
    const search = displayPanel ? encodeData($('#authorSearch').val()) : encodeData($('#authorSearchModal').val());

    if (displayPanel)
        $('#authorSearchModal').val($('#authorSearch').val());
    else
        $('#authorSearch').val($('#authorSearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_author.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#authorList,#authorListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="chooseAuthor(event)" name='author' class='pointer ${ authorSelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#authorList,#authorListModal').append(temp);
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

function chooseAuthor(e)
{
    $('p[name="author"]').removeClass('itemChoose');
    if (authorSelected !== e.target.innerText)
    {
        $(`p[name="author"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
        authorSelected = e.target.innerText;
    }
    else
        authorSelected = '';
    fetchBook();
}

function fetchPublisherList()
{
    const search = displayPanel ? encodeData($('#publisherSearch').val()) : encodeData($('#publisherSearchModal').val());

    if (displayPanel)
        $('#publisherSearchModal').val($('#publisherSearch').val());
    else
        $('#publisherSearch').val($('#publisherSearchModal').val());

    $.ajax({
        url: '/ajax_service/customer/book/get_publisher.php',
        method: 'GET',
        data: { search: search },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            if (data.query_result)
            {
                let temp = ``;
                $('#publisherList,#publisherListModal').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    temp += `<p onclick="choosePublisher(event)" name='publisher' class='pointer ${ publisherSelected === data.query_result[i].name ? 'itemChoose' : '' }'>${ data.query_result[i].name }</p>`
                }
                $('#publisherList,#publisherListModal').append(temp);
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

function choosePublisher(e)
{
    $('p[name="publisher"]').removeClass('itemChoose');
    if (publisherSelected !== e.target.innerText)
    {
        $(`p[name="publisher"]:contains("${ e.target.innerText }")`).addClass('itemChoose');
        publisherSelected = e.target.innerText;
    }
    else
        publisherSelected = '';
    fetchBook();
}

function fetchBook()
{
    const mode = $('#listOption').val();
    const limit = $('#listLimit').val();
    const search = $('#search_book').val();

    $.ajax({
        url: '/ajax_service/customer/book/get_book_list.php',
        method: 'GET',
        dataType: 'json',
        data: { search: encodeData(search), category: encodeData(categorySelected), author: encodeData(authorSelected), publisher: encodeData(publisherSelected), mode: encodeData(mode), limit: encodeData(limit), offset: encodeData(offset) },
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            else if (data.query_result)
            {
                $('button[name="previous"]').prop('disabled', data.query_result.length === 0 || offset === 1);
                $('button[name="next"]').prop('disabled', data.query_result.length === 0 || data.query_result.length < limit);

                let temp = ``;
                $('#bookList').empty();
                for (let i = 0; i < data.query_result.length; i++)
                {
                    if (i % itemPerRow === 0)
                        temp += '<div class="my-4 mx-auto d-flex rowContainer px-3">';
                    if (i % itemPerRow === 0)
                        temp += `<a class='cardHover bg-white border-1 card p-1 text-decoration-none ${ (i === data.query_result.length - 1 || itemPerRow === 1) ? '' : 'me-sm-4 me-3' }' href='/book/book-detail?id=${ data.query_result[i].id }'>
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
                    else if (i % itemPerRow === itemPerRow - 1)
                        temp += `<a class='cardHover bg-white border-1 card p-1 text-decoration-none ms-sm-4 ms-3' href='/book/book-detail?id=${ data.query_result[i].id }'>
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
                        temp += `<a class='cardHover bg-white border-1 card p-1 text-decoration-none ${ i === data.query_result.length - 1 ? 'ms-sm-4 ms-3' : 'mx-sm-4 mx-3' }' href='/book/book-detail?id=${ data.query_result[i].id }'>
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
                    if (i % itemPerRow === itemPerRow - 1) temp += '</div>';
                }
                $('#bookList').append(temp);
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
}

function adJustOffset(next)
{
    if (next)
        offset++;
    else
        offset--;
    fetchBook();
}

function addNewBook()
{
    const name = encodeData($('#book_name').val());
    const author = encodeData($('#book_author').val());

    if (name === '')
    {
        reportCustomValidity($('#book_name').get(0), 'Please enter book name!');
        return;
    } else if (name.length > 255)
    {
        reportCustomValidity($('#book_name').get(0), 'Book name is no longer than 255 characters!');
        return;
    }

    if (author === '')
    {
        reportCustomValidity($('#book_author').get(0), 'Please enter author name!');
        return;
    }

    $.ajax({
        url: '/ajax_service/customer/book/request_new_book.php',
        method: 'POST',
        dataType: 'json',
        data: { name, author },
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            else if (data.query_result)
            {
                $('#book_name').val('');
                $('#book_author').val('');
                $('#successModal').modal('show');
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
}