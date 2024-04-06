
$(document).ready(function() {
    $('#category').change(function() {
        var category = $(this).val();

        $.ajax({
            url: '/ajax_service/customer/book/get-book.php',
            type: 'GET',
            data: { category: category },
            success: function(response) {
                var books = JSON.parse(response);
                console.log("it ran");
                // Clear the current list of books
                $('#bookList').empty();
                console.log(books);
                var html = '';
                for (var i = 0; i < books.length; i++) {
                    if (i % 3 === 0) {
                    html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                    // Add more HTML as needed...
                    }
                  
                  html += `<div class="col-11 col-md-6 col-xl-4">`;
                  imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                        html += '<div class="card w-75 mx-auto d-block">';
                        html += `<a href="book-detail?id=${books[i].bookID}">`; 
                       html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                           html += `<div class=\"card-body\">`;
                               html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                               if(books[i].discount > 0){
                                
                                                html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> ${books[i].discount} %</p>`;
                               }
                               html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                if(books[i].discount > 0){
                                    html += `<p class="price">E-book price: <span style="text-decoration: line-through;">${books[i].filePrice}$</span> ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                    html += `<p class="price">Physical price: <span style="text-decoration: line-through;">${books[i].physicalPrice}$</span> ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                }
                                else{
                                    html += `<p class="price">E-book price: ${books[i].filePrice}$</p>`;
                                    html += `<p class="price">Physical price: ${books[i].physicalPrice}$</p>`;
                                }
                               
                               html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                               html += `(${books[i].star})`;
                                
                           html += `</div>`;
                       html += `</a>`;
                       html += `</div>`;

                 html += `</div>`;

                 if (i % 3 === 2 || i === books.length - 1) {
                html += '</div>'; // Close the div opened when i % 3 === 0
                    }
                }
                $('#bookList').append(html)
            }
        });
    });
    $('#DisplayBook').change(function() {
        var Display = $(this).val();

        $.ajax({
            url: '/ajax_service/customer/book/display.php',
            type: 'GET',
            data: { Display: Display },
            success: function(response) {
                var books = JSON.parse(response);
                console.log("it ran");
                // Clear the current list of books
                $('#bookList').empty();
                var html = '';
                for (var i = 0; i < books.length; i++) {
                    if (i % 3 === 0) {
                    html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                    // Add more HTML as needed...
                    }
                  
                  html += `<div class="col-11 col-md-6 col-xl-4">`;
                  imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                        html += '<div class="card w-75 mx-auto d-block">';
                        html += `<a href="book-detail?id=${books[i].id}">`; 
                       html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                           html += `<div class=\"card-body\">`;
                               html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                               if(books[i].discount > 0){
                                
                                                html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> ${books[i].discount} %</p>`;
                               }
                               html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                if(books[i].discount > 0){
                                    html += `<p class="price">E-book price: <span style="text-decoration: line-through;">${books[i].filePrice}$</span> ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                    html += `<p class="price">Physical price: <span style="text-decoration: line-through;">${books[i].physicalPrice}$</span> ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                }
                                else{
                                    html += `<p class="price">E-book price: ${books[i].filePrice}$</p>`;
                                    html += `<p class="price">Physical price: ${books[i].physicalPrice}$</p>`;
                                }
                               
                               html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                               html += `(${books[i].star})`;
                                
                           html += `</div>`;
                       html += `</a>`;
                       html += `</div>`;

                 html += `</div>`;

                 if (i % 3 === 2 || i === books.length - 1) {
                html += '</div>'; // Close the div opened when i % 3 === 0
                    }
                }
                $('#bookList').append(html)
            }
        });
    });
    $('#Discount_Button').on('click', function() {
        $(this).toggleClass('on');
        getBooks();
    });
    $('#Best-Seller_Button').on('click', function() {
        $(this).toggleClass('on');
        getBestSeller();
    });

    $('#search_book').on('submit', function(event) {
    event.preventDefault();

    var query = $('#search-input').val();
     //var query = $(this).serialize();
        //console.log(query);
    $.ajax({
        url: '/ajax_service/customer/book/search.php',
        type: 'GET',
        data: { query: query },
        success: function(response) {
            var books = JSON.parse(response);
                // Clear the current list of books
                $('#bookList').empty();
                console.log("it searched");
                //console.log(response);
                var html = '';
                for (var i = 0; i < books.length; i++) {
                    if (i % 3 === 0) {
                    html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                    // Add more HTML as needed...
                    }
                  
                  html += `<div class="col-11 col-md-6 col-xl-4">`;
                  imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                        html += '<div class="card w-75 mx-auto d-block">';
                        html += `<a href="book-detail?id=${books[i].id}">`; 
                       html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                           html += `<div class=\"card-body\">`;
                               html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                               if(books[i].discount > 0){
                                
                                                html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> ${books[i].discount} %</p>`;
                               }
                               html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                if(books[i].discount > 0){
                                    html += `<p class= "price" style="text-decoration: line-through;"> E-book price:  ${books[i].filePrice}$ </p>`;
                                    html += `<p class= "price" style="text-decoration: line-through;"> Physical price:  ${books[i].physicalPrice}$ </p>`;
                                html += `<p class= "price" > E-book price:  ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$ </p>`;
                                html += `<p class= "price" > Physical price:  ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$ </p>`;
                               }
                               else{
                                html += `<p class= "price" > E-book price:  ${books[i].filePrice}$ </p>`;
                               html += `<p class= "price" > Physical price:  ${books[i].physicalPrice}$ </p>`;
                               }
                               
                               html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                               html += `(${books[i].star})`;
                                
                           html += `</div>`;
                       html += `</a>`;
                       html += `</div>`;

                 html += `</div>`;

                 if (i % 3 === 2 || i === books.length - 1) {
                html += '</div>'; // Close the div opened when i % 3 === 0
                    }
                }
                $('#bookList').append(html)
        }
    });
});

    function getBooks() {
    var isDiscountOn = $('#Discount_Button').hasClass('on');
    var url = isDiscountOn ? '/ajax_service/customer/book/discount.php' : '/ajax_service/customer/book/No-discount.php';

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
                var books = JSON.parse(response);
                console.log("it ran");
                // Clear the current list of books
                $('#bookList').empty();
                console.log("it ran");
                var html = '';
                for (var i = 0; i < books.length; i++) {
                    if (i % 3 === 0) {
                    html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                    // Add more HTML as needed...
                    }
                  
                  html += `<div class="col-11 col-md-6 col-xl-4">`;
                  imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                        html += '<div class="card w-75 mx-auto d-block">';
                        html += `<a href="book-detail?id=${books[i].id}">`; 
                       html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                           html += `<div class=\"card-body\">`;
                               html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                               if(books[i].discount > 0){
                                
                                                html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> ${books[i].discount} %</p>`;
                               }
                               html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                if(books[i].discount > 0){
                                    html += `<p class="price">E-book price: <span style="text-decoration: line-through;">${books[i].filePrice}$</span> ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                    html += `<p class="price">Physical price: <span style="text-decoration: line-through;">${books[i].physicalPrice}$</span> ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                }
                                else{
                                    html += `<p class="price">E-book price: ${books[i].filePrice}$</p>`;
                                    html += `<p class="price">Physical price: ${books[i].physicalPrice}$</p>`;
                                }
                               
                               html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                               html += `(${books[i].star})`;
                                
                           html += `</div>`;
                       html += `</a>`;
                       html += `</div>`;

                 html += `</div>`;

                 if (i % 3 === 2 || i === books.length - 1) {
                html += '</div>'; // Close the div opened when i % 3 === 0
                    }
                }
                $('#bookList').append(html)
            }
        });
    }

    function getBestSeller() {
        var isDiscountOn = $('#Best-Seller_Button').hasClass('on');
        var url = isDiscountOn ? '/ajax_service/customer/book/best-seller.php' : '/ajax_service/customer/book/No-discount.php';

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                    var books = JSON.parse(response);
                    console.log("it ran");
                    // Clear the current list of books
                    $('#bookList').empty();
                    console.log("it ran");
                    var html = '';
                    for (var i = 0; i < books.length; i++) {
                        if (i % 3 === 0) {
                        html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                        // Add more HTML as needed...
                        }
                    
                    html += `<div class="col-11 col-md-6 col-xl-4">`;
                    imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                            html += '<div class="card w-75 mx-auto d-block">';
                            html += `<a href="book-detail?id=${books[i].id}">`; 
                        html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                            html += `<div class=\"card-body\">`;
                                html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                                if(books[i].discount > 0){
                                    
                                                    html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                            <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                </svg> ${books[i].discount} %</p>`;
                                }
                                html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                    if(books[i].discount > 0){
                                    html += `<p class="price">E-book price: <span style="text-decoration: line-through;">${books[i].filePrice}$</span> ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                    html += `<p class="price">Physical price: <span style="text-decoration: line-through;">${books[i].physicalPrice}$</span> ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                }
                                else{
                                    html += `<p class="price">E-book price: ${books[i].filePrice}$</p>`;
                                    html += `<p class="price">Physical price: ${books[i].physicalPrice}$</p>`;
                                }
                                
                                html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                                html += `(${books[i].star})`;
                                    
                            html += `</div>`;
                        html += `</a>`;
                        html += `</div>`;

                    html += `</div>`;

                    if (i % 3 === 2 || i === books.length - 1) {
                    html += '</div>'; // Close the div opened when i % 3 === 0
                        }
                    }
                    $('#bookList').append(html)
                }
            });
    }

    var currentPage = 1;
    var itemsPerPage = $('#itemsPerPage').val();

    function fetchBooks() {
        $.ajax({
            url: '/ajax_service/customer/book/item-per-page.php',
            type: 'GET',
            data: {
                itemsPerPage: itemsPerPage,
                page: currentPage
            },
            success: function(response) {
                var books = JSON.parse(response);

                // Clear the current list of books
                $('#bookList').empty();
                console.log("it ran");
                console.log(books);
                var html = '';
                for (var i = 0; i < books.length; i++) {
                    if (i % 3 === 0) {
                    html += '<div class="row justify-content-center align-items-center g-2 m-3">';
                    // Add more HTML as needed...
                    }
                  
                  html += `<div class="col-11 col-md-6 col-xl-4">`;
                  imagePath = `https://${window.location.host}/data/book/${books[i].pic} `;
                        html += '<div class="card w-75 mx-auto d-block">';
                        html += `<a href="book-detail?id=${books[i].id}">`; 
                       html += `<img src=" ${imagePath}" class="card-img-top" style="height: 28rem;" alt="...">`;
                           html += `<div class=\"card-body\">`;
                               html +=`<h5 class= "card-title"> Book: ${books[i].name} </h5>`;
                               if(books[i].discount > 0){
                                
                                                html +=`<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> ${books[i].discount} %</p>`;
                               }
                               html += `<p class= "author" > ${books[i].authorName} </p>` ;
                                if(books[i].discount > 0){
                                    html += `<p class="price">E-book price: <span style="text-decoration: line-through;">${books[i].filePrice}$</span> ${(books[i].filePrice - books[i].filePrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                    html += `<p class="price">Physical price: <span style="text-decoration: line-through;">${books[i].physicalPrice}$</span> ${(books[i].physicalPrice - books[i].physicalPrice * books[i].discount / 100).toFixed(2)}$</p>`;
                                }
                                else{
                                    html += `<p class="price">E-book price: ${books[i].filePrice}$</p>`;
                                    html += `<p class="price">Physical price: ${books[i].physicalPrice}$</p>`;
                                }
                               
                               
                               html += `<span class= "text-warning" > ${displayRatingStars(books[i].star)} </span>`;
                               html += `(${books[i].star})`;
                                
                           html += `</div>`;
                       html += `</a>`;
                       html += `</div>`;

                 html += `</div>`;

                 if (i % 3 === 2 || i === books.length - 1) {
                html += '</div>'; // Close the div opened when i % 3 === 0
                    }
                }
                $('#bookList').append(html)
            }
        });
    }

    $('#itemsPerPage').change(function() {
        itemsPerPage = $(this).val();
        currentPage = 1; // Reset to first page when items per page changes
        fetchBooks();
    });

    $('.page-link').click(function(e) {
        e.preventDefault();
        var page = $(this).text();

        if (page === '«') {
            currentPage = Math.max(1, currentPage - 1);
        } else if (page === '»') {
            currentPage += 1; // You might want to check if you're at the last page here
        } else {
            currentPage = parseInt(page);
        }

        fetchBooks();
    });
});