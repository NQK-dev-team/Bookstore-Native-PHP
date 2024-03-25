<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../tool/php/role_check.php';
require_once __DIR__ . '/../../tool/php/ratingStars.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require __DIR__ . '/../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require __DIR__ . '/../../error/403.php';
} else if ($return_status_code === 200) {
      require_once __DIR__. '/../../config/db_connection.php';
      require_once __DIR__. '/../../tool/php/converter.php';
      require_once __DIR__. '/../../tool/php/formatter.php';

      try{
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../error/500.php';
                  exit;
            }
            $elem = $conn->prepare('select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book inner join author on book.id = author.bookID
                                                                                                                                                                                  join fileCopy on book.id = fileCopy.id
                                                                                                                                                                                  join physicalCopy on book.id = physicalCopy.id
                                                                                                                                                                                  limit 8');
            $elem->execute();
            $elem = $elem->get_result();

            $featured = $conn->prepare('select distinct book.id, pSales, fSales, (pSales + fSales)  as sales, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book left join (select sum(amount) as pSales, physicalOrderContain.bookID from physicalOrderContain group by bookID) as physicalOrders on book.id = physicalOrders.bookID
                                                                                                                                                                                                                                                                        right join (select count(orderID) as fSales, fileOrderContain.bookID from fileOrderContain group by bookID) as fileOrders on book.id = fileOrders.bookID
                                                                                                                                                                                                                                                                        join author on book.id = author.bookID
                                                                                                                                                                                                                                                                        join fileCopy on book.id = fileCopy.id
                                                                                                                                                                                                                                                                        join physicalCopy on book.id = physicalCopy.id
                                                                                                                                                                                                                                                            order by sales DESC
                                                                                                                                                                                                                                                            limit 3');
            $featured->execute();
            $featured = $featured->get_result();

            $conn->close();
      }
      catch (Exception $e){
            http_response_code(500);
            require_once __DIR__ . '/../../error/500.php';
            exit;
      }
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require __DIR__ . '/../../head_element/cdn.php';
            require __DIR__ . '/../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Khoa">
            <meta name="description" content="Home page of NQK bookstore">
            <title>NQK Shop</title>

            <style>
                  .card:hover {
                        transform: scale(1.05);
                  } 
                  .author {
                        color: gray;
                  }
                  .pic {
                        height: 28rem;
                  }
                  a{
                        text-decoration: none;
                        color: black;
                  }
                  @media (min-width: 767.98px) { .card-body {
                  max-height: 205px; /* Adjust this value as needed */
                  overflow: auto; /* Add a scrollbar if the content is too long */
                  } 
                  .card-body::-webkit-scrollbar {
                  display: none;
                  }
            }
            .heading-decord{
                  font-weight: bold;
                  padding: 20px;
            }
            .bgr-col{
                  background-color: #F8F8FF;
            }
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/customer/header.php';
            ?>
            <section id="page">
            <br>
      <?php
            if($featured->num_rows > 0){
                  echo '<div class= "container border border-dark rounded bgr-col">';
                  echo '<p class="h1">Featured books</p>';
                  echo '<hr>';
                  echo '<div class="row justify-content-center align-items-center g-2 m-3 p-1">';
                  for($i = 1; $i <= $featured->num_rows; $i++){
                        $row=$featured->fetch_assoc();
                        if($i < 3){
                         $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                        echo ' <div class="col-9 col-md-6">';
                        echo "<a href=\"book/book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                        echo'                  <div class="card mx-2">
                                                <div class="row g-0">
                                                      <div class="col-md-7 d-flex justify-content-center ">';
                        echo '<img src="' . $imagePath . '" class="card-img my-3 px-3" style="height: 28rem;" alt="...">';
                        echo '
                              </div>
                              <div class="col-md-5">
                                    <div class="card-body" style="max-height: 350px;">
                                    <h5 class="card-title">'.$row["name"].'</h5>
                                    <p class="author">'.$row["authorName"].'</p>';
                                    echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                        echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                        echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                        echo "(".$row["star"].")";
                        echo '</div>
                              </div>
                              </div>
                        </div>';
                        echo '</a>
                                    </div>'; //end col-9 col-md-4
                        }
                        
                  }
                        echo "</div>"; //row end
                        
                  echo "</div>";  //container end
                  
            }
            else{
                  echo "Some error occured!";
            }
      ?>
      <h2 class="text-center heading-decord">Browse book</h2>
            <?php
            echo '<div class="container border border-dark rounded bgr-col mb-3">';
                  for ($i = 1; $i <= $elem->num_rows; $i++) {
                        if ($i % 4 == 1) {
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                        }
                        echo '<div class="col-9 col-md-3">';
                        $row = $elem->fetch_assoc();
                        // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                        $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                                                      echo '<div class="card w-75 mx-auto d-block ">';
                                                            echo "<a href=\"book/book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                                                            echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                                                            echo "<div class=\"card-body\">";
                                                                  echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                                                  echo "<p class=\"author\">".$row["authorName"]."</p>";
                                                                  echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                                  echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                                                  echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                                                  echo "(".$row["star"].")";
                                                                  
                                                            echo "</div>";
                                                      echo "</a>";
                                                      echo "</div>";
      
                        echo '</div>';
                        if ($i % 4 == 0 || $i == $elem->num_rows) {
                              echo '</div>';
                        }
                        }
                        echo '<a 
                        name=""
                        id=""
                        class="btn btn-primary d-flex justify-content-center align-items-center w-25 mx-auto m-3"
                        href="book"
                        role="button"
                        >Learn more</a>';
                        echo '</div>';
                        
            ?>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>