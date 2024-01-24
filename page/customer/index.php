<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../tool/php/role_check.php';

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
            $elem = $conn->prepare('select book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice from book inner join author on book.id = author.bookID
                                                                                                                                                                                  join fileCopy on book.id = fileCopy.id
                                                                                                                                                                                  join physicalCopy on book.id = physicalCopy.id');
            $elem->execute();
            $elem = $elem->get_result();

            $featured = $conn->prepare('select distinct physicalOrders.bookID, pSales, fSales, (pSales + fSales)  as sales, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice from customerOrder join (select sum(amount) as pSales, physicalOrder.id as id, bookID from physicalOrder join physicalOrderContain on physicalOrder.id = physicalOrderContain.orderID group by bookID) as physicalOrders on customerOrder.id = physicalOrders.id 
                                                                                                                                                                                                                                              join (select count(orderID) as fSales, fileOrder.id as id from fileOrder join fileOrderContain on fileOrder.id = fileOrderContain.orderID group by bookID) as fileOrders on customerOrder.id = fileOrders.id
                                                                                                                                                                                                                                              join author on physicalOrders.bookID = author.bookID
                                                                                                                                                                                                                                              join book on book.id = physicalOrders.bookID
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
      <html>

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
                  .card {
                        margin: 1rem;
                        width: 20rem;
                  }
                  .card:hover {
                        transform: scale(1.1);
                  } 
                  .author {
                        color: gray;
                  }
                  .pic {
                        height:auto;
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
                                          echo"<div class=\"grid-container\">";
                                          while($row=$featured->fetch_assoc()){
                                                // insert a card for link here
                                                 echo "<div class=\"card mb-3 border-light\">";
                                                      // insert picture here
                                                      echo "<img class=\"pic\" src=\"https://static.vecteezy.com/system/resources/thumbnails/002/219/582/small_2x/illustration-of-book-icon-free-vector.jpg\">";
                                                      echo "<div class=\"card-body\">";
                                                            echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                                            echo "<p class=\"author\">".$row["authorName"]."</p>";
                                                            echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                            echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                                            // echo "<p>"."Ratings: ".$row["stars"]."/5"."</p>";
                                                            // insert ratings here
                                                      echo "</div>";
                                                echo "</div>";
                                           }
                                          echo "</div>";
                                    }
                                    else{
                                          echo "Some error occured!";
                                    }
                              ?>
                        <h2>Browse book</h2>
                              <?php
                                    if($elem->num_rows > 0){
                                          echo"<div class=\"grid-container\">";
                                          while($row=$elem->fetch_assoc()){
                                                // insert a card for link here
                                                 echo "<div class=\"card mb-3 border-light\">";
                                                      // insert picture here
                                                      echo "<img class=\"pic\" src=\"https://static.vecteezy.com/system/resources/thumbnails/002/219/582/small_2x/illustration-of-book-icon-free-vector.jpg\">";
                                                      echo "<div class=\"card-body\">";
                                                            echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                                            echo "<p class=\"author\">".$row["authorName"]."</p>";
                                                            echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                            echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                                            // echo "<p>"."Ratings: ".$row["stars"]."/5"."</p>";
                                                            // insert ratings here
                                                      echo "</div>";
                                                echo "</div>";
                                           }
                                          echo "</div>";
                                    }
                                    else{
                                          echo "Can't find the thing you need!";
                                    }
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