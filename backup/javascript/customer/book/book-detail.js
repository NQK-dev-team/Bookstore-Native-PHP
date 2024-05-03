let mode = null, interval = null;
let ratingFilter = null;
let rating = null;
const limit = 5;
let showAll = false;

$(document).ready(function ()
{
    $('#addToCartForm').submit(function (e)
    {
        e.preventDefault();
        addToCart();
    });

    $('#ebook').on('change', function (e)
    {
        if (e.target.checked)
        {
            mode = 1;
            $('#amountDisplayer').css('display', 'none');
            $('#ebookPrice').css('display', 'flex');
            $('#hardcoverPrice').css('display', 'none');
            clearInterval(interval);
            $('#addToCartBtn').prop('disabled', false);
        }
    });

    $('#hardcover').on('change', function (e)
    {
        if (e.target.checked)
        {
            mode = 2;
            $('#amountDisplayer').css('display', 'flex');
            $('#ebookPrice').css('display', 'none');
            $('#hardcoverPrice').css('display', 'flex');
            checkInStock();
            interval = setInterval(function ()
            {
                checkInStock();
            }, 10000);
        }
    });

    $('#ratingForm').submit(function (e)
    {
        e.preventDefault();
        if (rating === null)
        {
            $('#errorModal').modal('show');
            $('#error_message').text('Please rate the product!');
            return;
        }

        const comment = $('#comment').val();
        $.ajax({
            url: '/ajax_service/customer/book/submit_rating.php',
            method: 'POST',
            data: { id: encodeData(bookID), rating: encodeData(rating), comment: encodeData(comment) },
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
                if (data.query_result)
                {
                    $('#customMessage').text('Rating submitted!');
                    $('#customModal').modal('show');
                    originalRating = rating;
                    originalComment = comment;
                    fetchRatings();
                    $('#deleteBtn').css('display', 'inline-block');
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
    });

    rating = originalRating;

    resetRatingForm();

    ratingFilter = 'all';

    fetchRatings();
});

function adjustAmount(isIncrease)
{
    if (isIncrease)
    {
        $(`#book_ammount`).val(parseInt($(`#book_ammount`).val()) + 1);
        // const inStock = parseInt($(`#in_stock`).text());
        // if (parseInt($(`#book_ammount`).val()) > inStock)
        // {
        //     $(`#book_ammount`).val(inStock);
        // }
    }
    else
    {
        $(`#book_ammount`).val(parseInt($(`#book_ammount`).val()) - 1);
        // if (parseInt($(`#book_ammount`).val()) < 1)
        // {
        //     $(`#book_ammount`).val(1);
        // }
    }

    checkAmmount();
}

function checkAmmount(flag = false)
{
    const amount = parseInt($(`#book_ammount`).val());
    const inStock = parseInt($(`#in_stock`).text());

    if (flag)
    {
        clearCustomValidity($(`#book_ammount`).get(0));

        if (amount < 0)
        {
            reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be negative!");
            return;
        } else if (amount === 0)
        {
            reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be zero!");
            return;
        }
        else if (amount > inStock)
        {
            reportCustomValidity($(`#book_ammount`).get(0), "Book amount exceeds in stock amount!");
            return;
        }
    }

    if (amount < 1 && inStock >= 1)
    {
        $(`#book_ammount`).val(1);
    } else if (amount > inStock)
    {
        $(`#book_ammount`).val(inStock);
    }

    $('#addToCartBtn').prop('disabled', inStock === 0);
}

function checkInStock()
{
    $.ajax({
        url: '/ajax_service/customer/cart/get_in_stock.php',
        method: 'GET',
        data: { id: encodeData(bookID) },
        dataType: 'json',
        success: function (data)
        {
            if (data.error)
            {
                $('#errorModal').modal('show');
                $('#error_message').text(data.error);
            }
            else
            {
                $(`#in_stock`).text(data.query_result);
                checkAmmount(true);
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

async function addToCart()
{
    if (mode === 1)
    {
        let buyable = true;
        await $.ajax({
            url: '/ajax_service/customer/book/check_bought.php',
            method: 'GET',
            data: { id: encodeData(bookID) },
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
                    $('#customMessage').text('You have already bought this E-book!');
                    $('#customModal').modal('show');
                    buyable = false;
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

        if (!buyable) return;

        await $.ajax({
            url: '/ajax_service/customer/book/check_in_cart.php',
            method: 'GET',
            data: { id: encodeData(bookID) },
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
                    $('#customMessage').text('This E-book has already been added to your cart!');
                    $('#customModal').modal('show');
                    buyable = false;
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

        if (!buyable) return;

        $.ajax({
            url: '/ajax_service/customer/book/add_to_cart_file.php',
            method: 'POST',
            data: { id: encodeData(bookID) },
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
                if (data.query_result)
                {
                    $('#customMessage').text('E-book added to cart!');
                    $('#customModal').modal('show');
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
    else if (mode === 2)
    {
        const amount = parseInt($(`#book_ammount`).val());

        $.ajax({
            url: '/ajax_service/customer/book/add_to_cart_physical.php',
            method: 'POST',
            data: { id: encodeData(bookID), amount: encodeData(amount) },
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
                if (data.query_result)
                {
                    $('#customMessage').text('Hardcover added to cart!');
                    $('#customModal').modal('show');
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
}

function setRatingFilter(e, filterOption)
{
    if (e.target.checked)
    {
        ratingFilter = filterOption;
        showAll = false;
        fetchRatings();
    }
}

function selectRatingFilter(e)
{
    ratingFilter = e.target.value;
    showAll = false;
    fetchRatings();
}

function setRating(e, value)
{
    if (e.target.checked)
    {
        rating = value;
    }
}

async function fetchRatings()
{
    await getBookRating();

    $.ajax({
        url: '/ajax_service/customer/book/get_ratings.php',
        method: 'GET',
        data: { id: encodeData(bookID), rating: encodeData(ratingFilter), limit: encodeData(limit), showAll: encodeData(showAll) },
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
                if (showAll)
                {
                    $('#showBtn').empty();
                    $('#showBtn').append(`<button onclick='showAll=false; fetchRatings()' class='border-0 bg-white'>Show Less <i class="bi bi-chevron-up"></i></button>`);
                }
                else
                {
                    $('#showBtn').empty();
                    $('#showBtn').append(`<button onclick='showAll=true; fetchRatings()' class='border-0 bg-white'>Show All <i class="bi bi-chevron-down"></i></button>`);
                }

                if (data.query_result[0].length)
                    $('#showBtn').css('display', 'inline-block');
                else
                    $('#showBtn').css('display', 'none');

                if (limit >= data.query_result[1])
                    $('#showBtn').css('display', 'none');
                else
                    $('#showBtn').css('display', 'inline-block');

                let temp = ``;

                $('#ratingList').empty();

                for (let i = 0; i < data.query_result[0].length; i++)
                {
                    temp += `<div class='borderBottom py-3'>
                                          <div class='d-flex'>
                                                <img alt='User profile image' src='${ data.query_result[0][i].imagePath }' class='ratingImage'>
                                                <div class='ms-2'>
                                                      <p class='mb-0 fw-medium'>${ data.query_result[0][i].name }</p>
                                                      <div class='text-warning'>${ displayRatingStars(data.query_result[0][i].star) }</div>
                                                      <small class='text-secondary'>${ data.query_result[0][i].ratingTime }</small>
                                                      <p class='mt-3'>${ data.query_result[0][i].comment }</p>
                                                </div>
                                          </div>
                                    </div>`;
                }
                $('#ratingList').append(temp);
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

function resetRatingForm()
{
    if (originalRating === 1)
    {
        $('#1-star').prop('checked', true);
    }
    else if (originalRating === 2)
    {
        $('#2-stars').prop('checked', true);
    }
    else if (originalRating === 3)
    {
        $('#3-stars').prop('checked', true);
    }
    else if (originalRating === 4)
    {
        $('#4-stars').prop('checked', true);
    }
    else if (originalRating === 5)
    {
        $('#5-stars').prop('checked', true);
    } else
    {
        $('#1-star').prop('checked', false);
        $('#2-stars').prop('checked', false);
        $('#3-stars').prop('checked', false);
        $('#4-stars').prop('checked', false);
        $('#5-stars').prop('checked', false);
    }

    $('#comment').val(originalComment);
}

async function getBookRating()
{
    await $.ajax({
        url: '/ajax_service/customer/book/get_book_rating.php',
        method: 'GET',
        data: { id: encodeData(bookID) },
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
                $('#bookAvgRating').empty();
                const temp = data.query_result[0] ? data.query_result[0] : 0;
                $('#bookAvgRating').append(`<span class="text-warning fw-medium">${ displayRatingStars(temp) }</span>(${ temp })`);
                $('#avgRating').text(temp);
                $('#avgRatingPanel').empty();
                $('#avgRatingPanel').append(displayRatingStars(temp));
                $('#totalRatings').text('(' + data.query_result[1] + ')');
                if (data.query_result[1] === 0)
                {
                    $('#noRating').css('display', 'block');
                    $('#showBtn').css('display', 'none');
                }
                else
                {
                    $('#noRating').css('display', 'none');
                    $('#showBtn').css('display', 'inline-block');
                }
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

function deleteRating()
{
    $.ajax({
        url: '/ajax_service/customer/book/delete_rating.php',
        method: 'DELETE',
        data: { id: encodeData(bookID) },
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
            if (data.query_result)
            {
                $('#customMessage').text('Rating deleted!');
                $('#customModal').modal('show');
                fetchRatings();
                $('#deleteBtn').css('display', 'none');
                originalRating = null;
                rating = null;
                originalComment = '';
                resetRatingForm();
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