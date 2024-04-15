$('#add_to_cart').on('click', function(event) {
    event.preventDefault();

    var bookId = $(this).data('book-id');
    var userId = $(this).data('user-id');
    alert('The book has been added to your cart.');
    // Now you can use bookId and userId in your AJAX request or any other logic...
    console.log('Book ID:', bookId, 'User ID:', userId);
    $.ajax({
        url: '/ajax_service/customer/book/add_to_cart.php',
        type: 'POST',
        data: { book_id: bookId, user_id: userId },
        success: function(response) {
            // Handle the response...
            console.log(response);
        }
    });
});

$('#add_to_cart_physical').on('click', function(event) {
    event.preventDefault();

    var bookId = $(this).data('book-id');
    var userId = $(this).data('user-id');
    var quantity = $('#quantity').val();
    alert('The books has been added to your cart.');
    // Now you can use bookId, userId, and quantity in your AJAX request or any other logic...
    console.log('Book ID:', bookId, 'User ID:', userId, 'Quantity of physical copies to cart:', quantity);
    $.ajax({
        url: '/ajax_service/customer/book/add_to_cart_physical.php',
        type: 'POST',
        data: { book_id: bookId, user_id: userId, quantity: quantity},
        success: function(response) {
            // Handle the response...
            console.log(response);
        }
    });
});

document.getElementById('button-decrease').addEventListener('click', function() {
    var quantity = document.getElementById('quantity');
    if (quantity.value > 1) {
        quantity.value--;
    }
});

document.getElementById('button-increase').addEventListener('click', function() {
    var quantity = document.getElementById('quantity');
    quantity.value++;
});

document.getElementById('avg-rating').addEventListener('mouseover', function() {
    document.getElementById('rating-container').style.display = 'block';
});

document.getElementById('avg-rating').addEventListener('mouseout', function() {
    document.getElementById('rating-container').style.display = 'none';
});

document.getElementById('rating-container').addEventListener('mouseover', function() {
    document.getElementById('rating-container').style.display = 'block';
});

document.getElementById('rating-container').addEventListener('mouseout', function() {
    document.getElementById('rating-container').style.display = 'none';
});



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