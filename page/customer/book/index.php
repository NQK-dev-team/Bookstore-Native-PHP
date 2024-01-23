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

            $stmt = $conn->prepare('select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath from book where book.status=1 order by book.name,book.id limit 20');
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

      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <h1 style="text-align: center;">Customer book page</h1>
            <div class="container">
      <div class="row">
            <div class="col">
            <select class="form-select" aria-label="Default select example">
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
            <div class="col">
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
            <div class="col">
            <button type="button" class="btn btn-primary">SEARCH NOW</button>
            </div>
      </div>
      
</div>
            
        
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>