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
    $(".btn-primary").click(function(){
        $(".collapse").collapse('toggle');
    });
});

function toggleButtonText() {
    var button = document.getElementById('toggleButton');
    if (button.textContent === "Show all comments") {
          button.textContent = "Show less comments";
    } else {
          button.textContent = "Show all comments";
    }
    }

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
                    const divVar = $(`<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Notice</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    You have already bought this E-book!
                              </div>
                        </div>`);
                    $('#toasts_container').append(divVar);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(divVar[0]);
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
                    const divVar = $(`<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Notice</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    This E-book has already been added to your cart!
                              </div>
                        </div>`);
                    $('#toasts_container').append(divVar);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(divVar[0]);
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
                    const divVar = $(`<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Success</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    E-book added to cart!
                              </div>
                        </div>`);
                    $('#toasts_container').append(divVar);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(divVar[0]);
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
                    const divVar = $(`<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                              <div class="toast-header bg-warning-subtle">
                                    <strong class="me-auto"><i class="bi bi-bell fs-5"></i> Success</strong>
                                    <small>Just now</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                              </div>
                              <div class="toast-body bg-warning-subtle">
                                    Hardcover added to cart!
                              </div>
                        </div>`);
                    $('#toasts_container').append(divVar);

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(divVar[0]);
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

document.querySelectorAll('.rating .bi').forEach((star, index, starList) => {
    star.addEventListener('mouseover', function() {
        // Change this star and all previous stars to filled stars
        for (let i = 0; i <= index; i++) {
            starList[i].classList.remove('bi-star');
            starList[i].classList.add('bi-star-fill');
        }
        // Change all next stars to empty stars
        for (let i = index + 1; i < starList.length; i++) {
            starList[i].classList.remove('bi-star-fill');
            starList[i].classList.add('bi-star');
        }
    });

    star.addEventListener('click', function() {
    const rating = this.getAttribute('data-value');
    const ratingHolder = document.getElementById('rating-holder');
    const bookId = this.getAttribute('data-book-id');
    const userId = this.getAttribute('data-user-id');
    const ratingResponse = document.getElementById('rating-response');
    // Clear the rating holder
    //ratingHolder.innerHTML = '';

    // Add the filled stars to the rating holder
    // for (let i = 0; i < rating; i++) {
    //     const star = document.createElement('i');
    //     star.className = 'bi bi-star-fill';
    //     ratingHolder.appendChild(star);
    // }
        // Send the rating to the server
        $.ajax({
            url: '/ajax_service/customer/book/rating.php',
            type: 'POST',
            data: { rating: rating, book_id: bookId, user_id: userId },
            success: function(response) {
                console.log(rating, bookId, userId);
                console.log(response);
                ratingResponse.innerHTML = response;
                if (response.trim() === 'Rating saved successfully.') {
                    ratingHolder.innerHTML = '';

                    //Add the filled stars to the rating holder
                    for (let i = 0; i < rating; i++) {
                        const star = document.createElement('i');
                        star.className = 'bi bi-star-fill';
                        ratingHolder.appendChild(star);
                    }
                }
            },
            error: function(error) {
                console.error('Error:', error);
                
            }
        });
    });
});



// Reset stars to empty when mouse leaves the rating div
document.querySelector('.rating').addEventListener('mouseleave', function() {
    document.querySelectorAll('.rating .bi').forEach(star => {
        star.classList.remove('bi-star-fill');
        star.classList.add('bi-star');
    });
});

function checkAmmount() {
        const amount = parseInt($('#quantity').val());
        const inStock = parseInt($('#inStock').text());

        // console.log('Amount:', amount);
        // console.log('In stock:', inStock);
        // console.log($('#quantity').get(0));

        clearCustomValidity($('#quantity').get(0));

        if (amount < 0) {
            alert('Book amount can not be negative!');
            return;
        } else if (amount === 0) {
            alert('Book amount cannot be zero!');
            return;
        } else if (amount > inStock) {
            reportCustomValidity($('#quantity').get(0), "Book amount exceeds in stock amount!");
            // var input = $('#quantity').get(0);
            // input.setCustomValidity('Book amount exceeds in stock amount!');
            //alert('Book amount exceeds in stock amount!');
            console.log('Amount > inStock');
            return;
        }
    }
    