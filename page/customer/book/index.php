<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else {
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';
      require_once __DIR__ . '/../../../tool/php/formatter.php';

      try {
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
            $elem = '';

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
            filecopy.price
        FROM 
            book
        JOIN 
            author ON book.id = author.bookid
        JOIN 
            filecopy ON book.id = filecopy.id
        WHERE 
            book.status = 1
        ORDER BY 
            book.name, book.id');
            $stmt->execute();
            $result = $stmt->get_result();
            // $cate_re = $cate->get_result();
            // echo '<section id="page" class="container">';
            // while ($row = $result->fetch_assoc()) {
            //       $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
            // echo '<div class="card" style="width: 18rem;">';
            // echo '<img src="' . $imagePath . '" class="card-img-top" alt="...">';
            // echo '<div class="card-body">';
            // echo '<h5 class="card-title">' . $row['name'] . '</h5>';
            // echo '<p class="card-text">Edition: ' . $row['edition'] . '</p>';
            // // Output other fields as needed...
            // echo '</div>';
            // echo '</div>';
            // }
            // echo '</section>';
            $cate = $conn->prepare('SELECT category.ID, category.name FROM category');
            $auth = $conn->prepare('SELECT author.authorName FROM author');
      } catch (Exception $e) {
            http_response_code(500);
            require_once __DIR__ . '/../../../error/500.php';
            exit;
      }
?>

      <!DOCTYPE html>
      <html>

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <title>Book list</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <h1 style="text-align: center;">Customer book page</h1>
      <div class="container">
            <div class="row">
                  <div class="col-12 col-md-4">
                        <!-- category form -->
                  <select class="form-select " aria-label="Default select example">
                        <option selected>Category</option>
                        <?php 
                              if ($cate) {
                                    $success = $cate->execute();
                                    if ($success) {
                                          $result1 = $cate->get_result();
                                          while ($row = $result1->fetch_assoc()) {
                                                // Process each row of data here...
                                                echo '<option value="' . $row['ID'].'">'. $row['name'] . '</option>';
                                          }
                                                } else {
                                          echo "Error executing statement: " . $conn->error;
                                                }     
                                          } else {
                                          echo "Error preparing statement: " . $conn->error;
                                          }
                        ?>
                        </select>  
                  </div>
                  <!-- author form -->
                  <div class="col-12 col-md-4">
                  <select class="form-select" aria-label="Default select example">
                        <option selected>Author</option>
                        <?php 
                              if ($auth) {
                                    $success1 = $auth->execute();
                                    if ($success1) {
                                          $result2 = $auth->get_result();
                                          while ($row = $result2->fetch_assoc()) {
                                                // Process each row of data here...
                                                echo '<option value="' . $row['authorName'].'">'. $row['authorName'] . '</option>';
                                          }
                                                } else {
                                          echo "Error executing statement: " . $conn->error;
                                                }     
                                          } else {
                                          echo "Error preparing statement: " . $conn->error;
                                          }
                        ?>
                        </select>  
                  </div>
                  <!-- search button -->
                  <div class="col-12 col-md-4">
                  <button type="button" class="btn btn-primary mx-auto d-block">SEARCH NOW</button>
                  </div>
            </div>
            <br>
            <?php
                  for ($i = 1; $i <= $result2->num_rows; $i++) {
                  if ($i % 3 == 1) {
                        echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                  }
                  echo '<div class="col-9 col-md-4">';
                  $row = $result->fetch_assoc();
                  $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
                  echo '<div class="card w-75 mx-auto d-block">';
                  echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                  echo '<div class="card-body">';
                  echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                  if($row['edition'] == 1){
                        echo '<p class="card-text">' . $row['edition'] . 'rst edition</p>';
                  }
                  
                  elseif($row['edition'] == 2){
                        echo '<p class="card-text">' . $row['edition'] . 'nd edition</p>';
                  }
                  elseif($row['edition'] == 3){
                        echo '<p class="card-text">' . $row['edition'] . 'rd edition</p>';
                  }
                  else{
                        echo '<p class="card-text">' . $row['edition'] . 'th edition</p>';
                  }
                  // Output other fields as needed...
                  echo '<p class="card-text">Written by: ' . $row['authorName'] . '</p>';
                  echo '<p class="card-text text-warning">';
                  if($row['avgRating'] <1){
                        echo '<i class="bi bi-star-half"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 1 && $row['avgRating'] <1.5){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 1.5 && $row['avgRating'] <2){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-half"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 2 && $row['avgRating'] <2.5){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 2.5 && $row['avgRating'] <3){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-half"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 3 && $row['avgRating'] <3.5){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 3.5 && $row['avgRating'] <4){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-half"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 4 && $row['avgRating'] <4.5){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star"></i>';
                  }
                  elseif($row['avgRating'] >= 4.5 && $row['avgRating'] <5){
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-fill"></i>';
                        echo '<i class="bi bi-star-half"></i>';
                  }
                  echo '</p>';
                  echo '<p class="card-text">Digital copy: ' . $row['price'] . '$</p>';
                  // echo '<div class="card-body d-flex justify-content-center align-items-center">' 
                  // . '<a href="#" class="card-link" style="font-size: 30px;"> <i class="bi bi-cart"></i></a>'
                  // . '<a href="#" class="card-link" style="font-size: 30px;"><i class="bi bi-heart"></i> </a>'
                  // . '</div>';
                  echo '<a
                        name=""
                        id=""
                        class="btn btn-primary d-flex justify-content-center align-items-center"
                        href="book-detail-page?bookID=' . $row['id'] . '"
                        role="button"
                        >Learn more</a>';

                  echo '</div>';
                  echo '</div>';

                  echo '</div>';
                  if ($i % 3 == 0 || $i == $result2->num_rows) {
                        echo '</div>';
                  }
                  }
                  
                  
            ?>
            
      
      </div>
            
        
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>