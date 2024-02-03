<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../tool/php/role_check.php';
require_once __DIR__ . '/../../tool/php/ratingStars.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../error/403.php';
} else {
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

            <meta name="author" content="Quang Nguyen">
            <meta name="description" content="Home page of NQK bookstore">
            <title>NQK Shop</title>

            <style>
                  .grid-container {
                        display: grid;
                        grid-template-columns: auto auto auto auto;
                        justify-content: space-evenly;
                        align-content: center;
                  }
                  .card:hover {
                        transform: scale(1.1);
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
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div>
                        <h1 class="text-center">Welcome to our shop</h1>
                        <h2>Featured books</h2>
                              <?php
                                    if($featured->num_rows > 0){
                                          echo"<div class=\"container\">";
                                          echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                                          while($row=$featured->fetch_assoc()){
                                                // insert a card for link here
                                                echo '<div class="col-9 col-md-4">';
                                                $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                                                echo '<div class="card w-75 mx-auto d-block">';
                                                echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                                                echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                                                     echo "<div class=\"card-body\">";
                                                           echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                                           echo "<p class=\"author\">".$row["authorName"]."</p>";
                                                           echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                           echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                                           // $cnt = 1;
                                                           // $res="";
                                                           // while($cnt <= 5){
                                                           //       if ($cnt > $row["star"]){
                                                           //             if($cnt - $row["star"] > 0 && $cnt - $row["star"] < 1){
                                                           //                   $res .= "<i class=\"bi bi-star-half\"></i>";
                                                           //             }
                                                           //             else{
                                                           //                   $res .= "<i class=\"bi bi-star\"></i>";
                                                           //             }
                                                           //       }
                                                           //       else {
                                                           //             $res .= "<i class=\"bi bi-star-fill\"></i>";
                                                           //       }
                                                           //       $cnt++;
                                                           // }
                                                           echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                                           echo "(".$row["star"].")";
                                                           
                                                     echo "</div>";
                                               echo "</a>";
                                               echo "</div>";

                                                echo "</div>";
                                           }
                                           echo "</div>";
                                          echo "</div>";
                                    }
                                    else{
                                          echo "Some error occured!";
                                    }
                              ?>
                        <h2>Browse book</h2>
                              <?php
                              echo '<div class="container">';
                                    for ($i = 1; $i <= $elem->num_rows; $i++) {
                                          if ($i % 4 == 1) {
                                                echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                                          }
                                          echo '<div class="col-9 col-md-3">';
                                          $row = $elem->fetch_assoc();
                                          // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                                          $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                                                                        echo '<div class="card w-75 mx-auto d-block">';
                                                                         echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
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