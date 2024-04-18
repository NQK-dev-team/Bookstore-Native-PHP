let mode = null, interval = null;

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

function checkAmmount()
{
    const amount = parseInt($(`#book_ammount`).val());
    const inStock = parseInt($(`#in_stock`).text());

    // clearCustomValidity($(`#book_ammount`).get(0));

    // if (amount < 0)
    // {
    //     reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be negative!");
    //     $('#addToCartBtn').prop('disabled', true);
    //     return;
    // } else if (amount === 0)
    // {
    //     reportCustomValidity($(`#book_ammount`).get(0), "Book amount can not be zero!");
    //     $('#addToCartBtn').prop('disabled', true);
    //     return;
    // }
    // else if (amount > inStock)
    // {
    //     reportCustomValidity($(`#book_ammount`).get(0), "Book amount exceeds in stock amount!");
    //     $('#addToCartBtn').prop('disabled', true);
    //     return;
    // }

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
                checkAmmount();
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
                    $('#liveToast').remove();
                    $('#toasts_container').append(`<div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Notice</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    You have already bought this E-book!
                              </div>
                        </div>`);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance($('#liveToast').get(0));
                    toastBootstrap.show();
                    buyable = false;
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
                    $('#liveToast2').remove();
                    $('#toasts_container').append(`<div id="liveToast2" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Notice</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    This E-book has already been added to your cart!
                              </div>
                        </div>`);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance($('#liveToast2').get(0));
                    toastBootstrap.show();
                    buyable = false;
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
                    $('#liveToast1').remove();
                    $('#toasts_container').append(`<div id="liveToast1" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Success</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    E-book added to cart!
                              </div>
                        </div>`);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance($('#liveToast1').get(0));
                    toastBootstrap.show();
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
                    $('#liveToast1').remove();
                    $('#toasts_container').append(`<div id="liveToast1" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Success</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    Hardcover added to cart!
                              </div>
                        </div>`);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance($('#liveToast1').get(0));
                    toastBootstrap.show();
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
}