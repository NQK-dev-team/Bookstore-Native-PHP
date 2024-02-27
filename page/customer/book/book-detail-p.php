<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
            require_once __DIR__ . '/../../../config/db_connection.php';
            require_once __DIR__ . '/../../../tool/php/converter.php';
            require_once __DIR__ . '/../../../tool/php/formatter.php';
      
            try {
                  // Get book id from URL
                  $bookID = $_GET['bookID'];
                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
                  $stmt = $conn->prepare('SELECT DISTINCT 
            book.id,
            book.name,
            book.edition,
            book.isbn,
            book.ageRestriction,
            book.avgRating,
            book.publisher,
            book.publishDate,
            book.description,
            book.imagePath,
            author.authorName,
            physicalcopy.price
        FROM 
            book
        JOIN 
            author ON book.id = author.bookid
        JOIN 
        physicalcopy ON book.id = physicalcopy.id
        WHERE 
            book.status = 1
        ORDER BY 
            book.name, book.id');
                  $stmt->execute();
                  $result = $stmt->get_result();
                  // $book = $result->fetch_assoc();
                  
            } catch (Exception $e) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Anh Khoa">
            <meta name="description" content="Home page of NQK bookstore">
            <title>Book detail</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <?php
                  while ($book = $result->fetch_assoc()) {
                  if($bookID == $book['id']){
                  $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($book['imagePath']));
                        echo ' <div class="container">';
                        echo '<div class="row justify-content-center align-items-center g-2 mt-3">';
                        echo '<p class="h1">Book Detail</p>';
                        echo '<hr>';
                        echo'</div>';
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">
                                    <div class="col-10 col-md-6 d-flex justify-content-center align-items-center">';
                                    echo '<img src="' . $imagePath . '" class="card-img-top w-50 rounded" alt="..."> </div>';
                                    echo '<div class="col-10 col-md-6"> ';
                                    echo '<h2 class="display-4">' . $book['name'] . '</h2>';
                                    if($book['edition'] == 1){
                                          echo '<p class="h6">' . $book['edition'] . 'rst edition</p>';
                                    }
                                    
                                    elseif($book['edition'] == 2){
                                          echo '<p class="h6">' . $book['edition'] . 'nd edition</p>';
                                    }
                                    elseif($book['edition'] == 3){
                                          echo '<p class="h6">' . $book['edition'] . 'rd edition</p>';
                                    }
                                    else{
                                          echo '<p class="h6">' . $book['edition'] . 'th edition</p>';
                                    }
                                    echo '<p class="h3 text-danger">Physical copy: ' . $book['price'] . '$</p>';
                                    echo '<span class="text-warning">'.displayRatingStars($book['avgRating']).'</span>';
                                                           echo "(".$book['avgRating'].")";
                                    echo '<p class="h5">Author: ' . $book['authorName'] . '</p>';
                                    echo '<p class="h5">Publisher: ' . $book['publisher'] . '</p>';
                                    echo '<p class="h5">ISBN: ' . $book['isbn'] . '</p>';
                                    echo '<a
                                          name=""
                                          id=""
                                          class="btn btn-info text-light col-9 col-md-3 m-3"
                                          href="#"
                                          role="button"
                                          >Add to cart</a
                                    >';
                                    echo '<a
                                          name=""
                                          id=""
                                          class="btn btn-info col-9 text-light col-md-3 m-3"
                                          href="book-detail?bookID=' . normalizeURL(rawurlencode($book['id'])) . '"
                                          role="button"
                                          >Digital copy</a
                                    >';
                                    
                                    echo '</div>';
                              echo'</div>';
                              
                              echo '<div class="row justify-content-center align-items-center g-2 mt-3">';
                                    echo '<div class="col-10"> ';
                                    echo '<p class="h6">Description: ' . $book['description'] . '</p>';
                                    echo'</div>';
                              echo'</div>';

                        echo '</div>'; 
                  }
                  
            }
                  ?>
                  
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>