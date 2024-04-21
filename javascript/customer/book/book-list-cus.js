
$(document).ready(function() {    
    var currentPage = 1;
    var itemsPerPage = $('#itemsPerPage').val();
    var Display = $('#DisplayBook').val();
    var query = $('#search-input').val();
    var listItems = document.querySelectorAll('#Category-list li');
    var selectedCategory ="";
    var listItemsAuth = document.querySelectorAll('#Author-list li');
    var selectedAuthor = "";
    var listItemsPub = document.querySelectorAll('#Publisher-list li');
    var selectedPub = "";

    //the main fetch book funciton
    function fetchBooks() {
        $.ajax({
            url: '/ajax_service/customer/book/item-per-page.php',
            type: 'GET',
            data: {
                itemsPerPage: itemsPerPage,
                page: currentPage,
                Display: Display,
                query: query,
                selectedCategory: selectedCategory,
                selectedAuthor: selectedAuthor,
                selectedPub: selectedPub
            },
            success: function(response) {
                var books = JSON.parse(response);

                // Clear the current list of books
                $('#bookList').empty();
                console.log("Fetched books", "Category: ", selectedCategory, "Items per page: ", itemsPerPage, "Page: ", currentPage, "Display: ", Display, "Query: ", query);
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

    //when search for the category
    $('#Category-search').on('input', function(){
        var SearchedCategory = $('#Category-search').val();
        $('#Category-list').empty();
        $.ajax({
            url: '/ajax_service/customer/book/category-search.php',
            type: 'GET',
            data: {
                SearchedCategory: SearchedCategory
            },
            success: function(response) {
                var categories = JSON.parse(response);
                var html = '';
                for (var i = 0; i < categories.length; i++) {
                    html += `<li>${categories[i].name}</li>`;
                }
                $('#Category-list').append(html);
                listItems = document.querySelectorAll('#Category-list li');
                console.log("Inside the ajax:", listItems);
                listItems.forEach(function(item) {
                item.addEventListener('click', function() {
                        selectedCategory = this.textContent;
                        selectedCategory = selectedCategory.replace("'", "\\'");
                        console.log(selectedCategory);
                        itemsPerPage = $('#itemsPerPage').val();
                        query = $('#search-input').val();
                        currentPage = 1; // Reset to first page when items per page changes
                        selectedAuthor = ""; //Reset the author
                        fetchBooks();

                        // Remove the glow class from all li elements
                        listItems.forEach(function(item) {
                            item.classList.remove('glow');
                        });

                        // Add the glow class to the clicked li element
                        this.classList.add('glow');
                    });
                });
            }
        });
    });
    //when search for the author
    $('#Author-search').on('input', function(){
        var SearchedAuth = $('#Author-search').val();
        $('#Author-list').empty();
        console.log(SearchedAuth);
        $.ajax({
            url: '/ajax_service/customer/book/author-search.php',
            type: 'GET',
            data: {
                SearchedAuth: SearchedAuth
            },
            success: function(response) {
                var authors = JSON.parse(response);
                console.log(authors);
                var html = '';
                for (var i = 0; i < authors.length; i++) {
                    html += `<li>${authors[i].authorName}</li>`;
                }
                $('#Author-list').append(html);
                listItemsAuth = document.querySelectorAll('#Author-list li');
                console.log("Inside the ajax:", listItemsAuth);
                listItemsAuth.forEach(function(item) {
                    item.addEventListener('click', function() {
                        selectedAuthor = this.textContent;
                        selectedAuthor = selectedAuthor.replace("'", "\\'");
                        console.log(selectedAuthor);
                        itemsPerPage = $('#itemsPerPage').val();
                        query = $('#search-input').val();
                        currentPage = 1; // Reset to first page when items per page changes
                        selectedCategory =""; //reset the category
                        fetchBooks();

                        // Remove the glow class from all li elements
                        listItemsAuth.forEach(function(item) {
                            item.classList.remove('glow');
                        });

                        // Add the glow class to the clicked li element
                        this.classList.add('glow');
                    });
                });
            }
        });
    });
    //when search for the publisher
    $('#Publisher-search').on('input', function(){
        var SearchedPub = $('#Publisher-search').val();
        $('#Publisher-list').empty();
        console.log(SearchedPub);
        $.ajax({
            url: '/ajax_service/customer/book/publisher-search.php',
            type: 'GET',
            data: {
                SearchedPub: SearchedPub
            },
            success: function(response) {
                var pubs = JSON.parse(response);
                console.log(pubs);
                var html = '';
                for (var i = 0; i < pubs.length; i++) {
                    html += `<li>${pubs[i].publisher}</li>`;
                }
                $('#Publisher-list').append(html);
                listItemsPub = document.querySelectorAll('#Publisher-list li');
                console.log("Inside the ajax:", listItemsPub);
                listItemsPub.forEach(function(item) {
                    item.addEventListener('click', function() {
                        selectedPub = this.textContent;
                        selectedPub = selectedPub.replace("'", "\\'");
                        console.log(selectedPub);
                        itemsPerPage = $('#itemsPerPage').val();
                        query = $('#search-input').val();
                        currentPage = 1; // Reset to first page when items per page changes
                        selectedCategory =""; //reset the category
                        selectedAuthor = ""; //reset the author
                        fetchBooks();

                        // Remove the glow class from all li elements
                        listItemsPub.forEach(function(item) {
                            item.classList.remove('glow');
                        });

                        // Add the glow class to the clicked li element
                        this.classList.add('glow');
                    });
                });
            }
        });
    });

    //when selecting the the category from the side pannel
    listItems.forEach(function(item) {
        item.addEventListener('click', function() {
            selectedCategory = this.textContent;
            selectedCategory = selectedCategory.replace("'", "\\'");
            console.log(selectedCategory);
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            selectedAuthor = ""; //Reset the author
            selectedPub = ""; //Reset the publisher
            fetchBooks();

            // Remove the glow class from all li elements
            listItems.forEach(function(item) {
                item.classList.remove('glow');
            });

            // Add the glow class to the clicked li element
            this.classList.add('glow');
        });
    });
    //when selecting the the author from the side pannel
    listItemsAuth.forEach(function(item) {
        item.addEventListener('click', function() {
            selectedAuthor = this.textContent;
            selectedAuthor = selectedAuthor.replace("'", "\\'");
            console.log(selectedAuthor);
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            selectedCategory =""; //reset the category
            selectedPub = ""; //reset the publisher
            fetchBooks();

            // Remove the glow class from all li elements
            listItemsAuth.forEach(function(item) {
                item.classList.remove('glow');
            });

            // Add the glow class to the clicked li element
            this.classList.add('glow');
        });
    });
    //when selecting the the publisher from the side pannel
    listItemsPub.forEach(function(item) {
        item.addEventListener('click', function() {
            selectedPub = this.textContent;
            selectedPub = selectedPub.replace("'", "\\'");
            console.log(selectedPub);
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            selectedCategory =""; //reset the category
            selectedAuthor = ""; //reset the author
            fetchBooks();

            // Remove the glow class from all li elements
            listItemsPub.forEach(function(item) {
                item.classList.remove('glow');
            });

            // Add the glow class to the clicked li element
            this.classList.add('glow');
        });
    });
    //url process
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('select')) {
        console.log("Select:", urlParams.get('select'));
        if(urlParams.get('select') == "discount"){
            Display = "Discount";
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            fetchBooks();
        }
        if(urlParams.get('select') == "sale"){
            Display = "Best-Seller";
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            fetchBooks();
        }
    }
    if (urlParams.has('category')) {
        console.log("Category:", urlParams.get('category'));
        if(urlParams.get('category') != null ){
            selectedCategory = urlParams.get('category');
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            fetchBooks();
        }
    }
    if (urlParams.has('publisher')) {
        console.log("Publisher:", urlParams.get('publisher'));
        if(urlParams.get('publisher') != null ){
            selectedPub = urlParams.get('publisher');
            itemsPerPage = $('#itemsPerPage').val();
            query = $('#search-input').val();
            currentPage = 1; // Reset to first page when items per page changes
            fetchBooks();
        }
    }


    $('#itemsPerPage').change(function() {
        itemsPerPage = $(this).val();
        currentPage = 1; // Reset to first page when items per page changes
        Display = $('#DisplayBook').val(); //fetch the current display value
        query = $('#search-input').val();
        fetchBooks();
    });

    $('#DisplayBook').change(function() {
        Display = $(this).val();
        itemsPerPage = $('#itemsPerPage').val();
        query = $('#search-input').val();
        currentPage = 1; // Reset to first page when items per page changes
        fetchBooks();
    });

    $('#search_book').on('submit', function(event) {
    event.preventDefault();

    query = $('#search-input').val();
    itemsPerPage = $('#itemsPerPage').val();
    currentPage = 1; // Reset to first page when items per page changes
    // var query = $('#search-input').val();
     //var query = $(this).serialize();
        //console.log(query);
    fetchBooks();
    });

    $('.page-link').click(function(e) {
        e.preventDefault();
        var page = $(this).text();

        if (page === '<') {
            currentPage = Math.max(1, currentPage - 1);
        } else if (page === '>') {
            currentPage += 1; // You might want to check if you're at the last page here
        } else {
            currentPage = parseInt(page);
        }

        fetchBooks();
    });

    // Initial fetch
    fetchBooks();
    //when the screen is under 576px, the filter will be moved to the modal
    $(window).on('resize', function() {
        var win = $(this); // this = window
        if (win.width() <= 576) {
            $('#hideable').appendTo('.modal-body');
        } else {
            $('#hideable').appendTo('#Desktop-pannel'); // replace #someElement with the id of the element where #hideable should be moved back to
        }
    });
});