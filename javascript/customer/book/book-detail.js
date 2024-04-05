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