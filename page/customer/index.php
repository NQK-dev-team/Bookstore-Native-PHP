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
                                                                                                                                                                                  limit 6');
            $elem->execute();
            $elem = $elem->get_result();

            $featured = $conn->prepare('select distinct book.id, pSales, fSales, (pSales + fSales)  as sales, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book left join (select sum(amount) as pSales, physicalOrderContain.bookID from physicalOrderContain group by bookID) as physicalOrders on book.id = physicalOrders.bookID
                                                                                                                                                                                                                                                                        right join (select count(orderID) as fSales, fileOrderContain.bookID from fileOrderContain group by bookID) as fileOrders on book.id = fileOrders.bookID
                                                                                                                                                                                                                                                                        join author on book.id = author.bookID
                                                                                                                                                                                                                                                                        join fileCopy on book.id = fileCopy.id
                                                                                                                                                                                                                                                                        join physicalCopy on book.id = physicalCopy.id
                                                                                                                                                                                                                                                            order by sales DESC
                                                                                                                                                                                                                                                            limit 5');
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
            .carousel-item{
                  height: 36rem;
                  color: white;
                  position: relative;
                  background-position: center;
                  background-size: cover;
                  
            }
            @media screen and (max-width: 768px) {
                  .carousel-item{
                        height: 50rem;
                  }
                  
            }
            .overlay-image{
                  position: absolute;
                  top: -10px;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  /* background-image:url(https://th.bing.com/th/id/R.9ab69065931f33912678c9fa0055c875?rik=l4n%2bZal8cVnKMg&pid=ImgRaw&r=0); */
                  background-position: center;
                  background-size: cover;
                  opacity: 0.70;
                  border-radius: 25px;
            }
            .carousel-control-prev-icon {
            filter: invert(1) grayscale(100%) brightness(200%);
            }
            .carousel-control-next-icon {
            filter: invert(1) grayscale(100%) brightness(200%);
            }
            .carousel-indicators{
            filter: invert(1) grayscale(100%) brightness(200%);
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
                  echo '<div class= "container border border-dark rounded bgr-col my-3">';
                  echo '<p class="h1">Featured books</p>';
                  echo '<hr>';
                  echo '<div class="row justify-content-center align-items-center g-2 m-3 p-1">';
                  $rows = array();
                  for($i = 0; $i <= $featured->num_rows; $i++){
                        $row=$featured->fetch_assoc();
                        $rows[] = $row;
                        if($i < 2){
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
                        echo '<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">';
for($i = 2; $i < $featured->num_rows; $i++){
      echo '<div class="carousel-item ';
      if($i == 2){
            echo ' active" data-interval="1500">';
      }
      else{
            echo '" data-interval="1000">';
      }
      echo '<div class="overlay-image m-3"> </div>';
      echo '<div class="row justify-content-center align-items-center g-2 m-3 p-1">';
      $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($rows[$i]['pic']));
                        echo ' <div class="col-10 col-md-10">';
                        echo "<a href=\"book/book-detail?bookID=".normalizeURL(rawurlencode($rows[$i]["id"]))."\">"; 
                        echo'                  <div class="card mx-2" style="opacity: 90%">
                                                <div class="row g-0">
                                                      <div class="col-md-7 d-flex justify-content-center ">';
                        echo '<img src="' . $imagePath . '" class="card-img w-75 my-3 px-3" style="height: 28rem;" alt="...">';
                        echo '
                              </div>
                              <div class="col-md-5">
                                    <div class="card-body" style="max-height: 350px;">
                                    <h5 class="card-title">'.$rows[$i]["name"].'</h5>
                                    <p class="author">'.$rows[$i]["authorName"].'</p>';
                                    echo "<p class=\"price\">"."E-book price: ".$rows[$i]["filePrice"]."$"."</p>";
                        echo "<p class=\"price\">"."Physical price: ".$rows[$i]["physicalPrice"]."$"."</p>";
                        echo '<span class="text-warning">'.displayRatingStars($rows[$i]["star"]).'</span>';
                        echo "(".$rows[$i]["star"].")";
                        echo '</div>
                              </div>
                              </div>
                        </div>';
                        echo '</a>
                                    </div>'; //end col-9 col-md-4
      echo '</div>';//end row
    echo '</div>'; //end carousel item
}

    //end of carousel inner
echo '</div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>';
                  echo "</div>";  //container end
                  
            }
            else{
                  echo "Some error occured!";
            }
      ?>
      
            <?php
            echo '<div class="container border border-dark rounded bgr-col my-3">';
            echo '<div class="row justify-content-center align-items-center g-2 m-3 p-1">';
            echo '<p class="h1 col-9 col-md-9">Our collection</p>';
            echo '<a 
                        name=""
                        id=""
                        class="btn btn-primary align-items-center w-25 mx-auto m-3 col-9 col-md-3"
                        href="book"
                        role="button"
                        style="font-size: 20px;"
                        >Learn more</a>';
            echo '</div>';//end div row
                  echo '<hr>';
                  for ($i = 1; $i <= $elem->num_rows; $i++) {
                        if ($i % 3 == 1) {
                              echo '<div class="row justify-content-center align-items-center g-2 m-3 py-3">';
                        }
                        echo '<div class="col-9 col-md-4">';
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
                        if ($i % 3 == 0 || $i == $elem->num_rows) {
                              echo '</div>';
                        }
                        }
                        
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