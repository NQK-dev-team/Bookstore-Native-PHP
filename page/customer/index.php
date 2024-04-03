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
            $elem = $conn->prepare('WITH RankedBooks AS (
  SELECT book.id, book.name,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM book
  INNER JOIN author ON book.id = author.bookID
  INNER JOIN fileCopy ON book.id = fileCopy.id
  INNER JOIN physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN eventapply ON book.id = eventapply.bookID
  LEFT JOIN eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1 limit 6');
            $elem->execute();
            $elem = $elem->get_result();

            $featured = $conn->prepare('WITH RankedBooks AS (
  SELECT book.id, book.name,book.isbn,book.publisher,book.publishDate,
  pSales, fSales, (pSales + fSales)  as sales,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM book
  left join (select sum(amount) as pSales, physicalOrderContain.bookID from physicalOrderContain group by bookID) as physicalOrders on book.id = physicalOrders.bookID
right join (select count(orderID) as fSales, fileOrderContain.bookID from fileOrderContain group by bookID) as fileOrders on book.id = fileOrders.bookID
  INNER JOIN author ON book.id = author.bookID
  INNER JOIN fileCopy ON book.id = fileCopy.id
  INNER JOIN physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN eventapply ON book.id = eventapply.bookID
  LEFT JOIN eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1 order by sales DESC limit 5');
            $featured->execute();
            $featured = $featured->get_result();
            
            $discounted_books = $conn->prepare('WITH RankedBooks AS (
  SELECT book.id, book.name,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM book
  INNER JOIN author ON book.id = author.bookID
  INNER JOIN fileCopy ON book.id = fileCopy.id
  INNER JOIN physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN eventapply ON book.id = eventapply.bookID
  LEFT JOIN eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1 AND discount != 0 limit 10');
            $discounted_books->execute();
            $discounted_books = $discounted_books->get_result();
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
                  .carousel-item-feature{
                        height: 38rem;
                        color: white;
                        position: relative;
                        background-position: center;
                        background-size: cover;
                        
                  }
                  .carousel-item-discount{
                              height: 38rem;
                        }
                  
                  
                  @media screen and (max-width: 1200px) {
                        .carousel-item-discount{
                              height: 75rem;
                        }
                        
                  }
                  @media screen and (max-width: 992px) {
                        .carousel-item-discount{
                              height: 75rem;
                        }
                        
                  }
                  @media screen and (max-width: 768px) {
                        .carousel-item-feature{
                              height: 70rem;
                        }
                        .carousel-item-discount{
                              height: 225rem;
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
                  .carousel-control-prev-icon-feature {
                  filter: invert(1) grayscale(100%) brightness(200%);
                  }
                  .carousel-control-next-icon-feature {
                  filter: invert(1) grayscale(100%) brightness(200%);
                  }
                  .carousel-indicators-feature{
                  filter: invert(1) grayscale(100%) brightness(200%);
                  }
                  .feature-card{
                        border: none;
                        background-color: #F8F8FF;
                  }
                  .feature-card:hover {
                  border: 1px solid black;
                  background-color: #FEFEFF;
                  }
                  .rounded-border{
                        border-radius: 20px;
                  }
                  .carousel-control-prev-discount {
                  width: 50px;
                  height: 50px;
                  background-color: black;
                  border-radius: 50%;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  position: absolute;
                  top: 50%;
                  }

                  .carousel-control-prev-icon-discount {
                  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3e%3cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
                  }
                  .carousel-control-next-discount {
                  width: 50px;
                  height: 50px;
                  background-color: black;
                  border-radius: 50%;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  top: 50%;
                  }

                  .carousel-control-next-icon-discount {
                  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
                  }
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/customer/header.php';
            ?>
            <section id="page">
            <br>
            
      <!-- feature books section -->
      <?php
            if($featured->num_rows > 0){
                  echo '<div class= "container-fluid w-75 border border-dark bgr-col my-3 rounded-border">';
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
                              echo' <div class="card mx-2 feature-card" >
                                          <div class="row g-0">
                                                            <div class="col-md-7 d-flex justify-content-center ">';
                                          echo '<img src="' . $imagePath . '" class="card-img my-3 px-3" style="height: 28rem; border-radius: 35px;" alt="...">';
                                          echo '
                                                      </div>
                                          <div class="col-md-5">
                                                <div class="card-body" style="max-height: 350px;">
                                                <h5 class="card-title">'.$row["name"].'</h5>';
                                                echo '<p class="author">'.$row["authorName"].'</p>';
                                                if($row["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg> '.$row["discount"].'%</p>';
                                    }
                                          
                                                if($row["discount"] > 0){
                                                echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $row["filePrice"] . '$</span> ' .round($row["filePrice"] - $row["filePrice"] * $row["discount"] / 100, 2). '$</p>';
                                                echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $row["physicalPrice"] . '$</span> ' .round($row["physicalPrice"] - $row["physicalPrice"] * $row["discount"] / 100, 2). '$</p>';
                                                }
                                                else {
                                                echo "<p class=\"price \">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                echo "<p class=\"price \">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                          }
                                          echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                          echo "(".$row["star"].")";
                                          echo '</div>
                                                </div>
                                          </div>
                                    </div>';//end card
                        echo '</a>
                                    </div>'; //end col-9 col-md-4
                        }
                        
                  }
                        echo "</div>"; //row end
                        echo '<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-indicators carousel-indicators-feature">
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                  <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                  </div>
                  <div class="carousel-inner">';
                  for($i = 2; $i < $featured->num_rows; $i++){
                        echo '<div class="carousel-item-feature carousel-item';
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
                                          echo'                  <div class="card mx-2 feature-card" style="opacity: 90%">
                                                                  <div class="row g-0">
                                                                        <div class="col-md-7 d-flex justify-content-center ">';
                                          echo '<img src="' . $imagePath . '" class="card-img w-75 my-3 px-3" style="height: 28rem; border-radius: 35px;" alt="...">';
                                          echo '
                                                </div>
                                                <div class="col-md-5">
                                                      <div class="card-body" style="max-height: 400px;">
                                                      <h5 class="card-title">'.$rows[$i]["name"].'</h5>';
                                                      echo '<p class="author">'.$rows[$i]["authorName"].'</p>';
                                                      if($rows[$i]["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg> '.$rows[$i]["discount"].'%</p>';
                                    }
                                                      
                                                      if($rows[$i]["discount"] > 0){
                                                echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $rows[$i]["filePrice"] . '$</span> ' .round($rows[$i]["filePrice"] - $rows[$i]["filePrice"] * $rows[$i]["discount"] / 100, 2). '$</p>';
                                                echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $rows[$i]["physicalPrice"] . '$</span> ' .round($rows[$i]["physicalPrice"] - $rows[$i]["physicalPrice"] * $rows[$i]["discount"] / 100, 2). '$</p>';
                                                }
                                                else {
                                                echo "<p class=\"price \">"."E-book price: ".$rows[$i]["filePrice"]."$"."</p>";
                                                echo "<p class=\"price \">"."Physical price: ".$rows[$i]["physicalPrice"]."$"."</p>";
                                          }
                                          echo '<span class="text-warning">'.displayRatingStars($rows[$i]["star"]).'</span>';
                                          echo "(".$rows[$i]["star"].")";
                                          echo '<p class=" mt-3">ISBN: ' .$rows[$i]['isbn'] . '</p>';
                                          echo '<p class="">Publisher: ' .$rows[$i]['publisher'] . '</p>';
                                          echo '<p class="">Publish date: ' .$rows[$i]['publishDate'] . '</p>';
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
                  <span class="carousel-control-prev-icon carousel-control-prev-icon-feature" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next carousel-control-next-icon-feature" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
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
      <!-- discounted books section -->
       <?php
            echo '<div class="bg-danger">';
            echo '<div class="container-fluid my-3">';
            echo '<div class="row justify-content-center align-items-center g-2 m-3 p-1">';
            echo '<p class="h1 col-9 col-md-9 text-light">Today sales</p>';
            echo '<a 
                        name=""
                        id=""
                        class="btn btn-light align-items-center w-25 mx-auto m-3 col-9 col-md-3 text-danger"
                        href="book"
                        role="button"
                        style="font-size: 20px;"
                        >Learn more</a>';
            echo '</div>';//end div row
                  echo '<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-indicators">
                  <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                  <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                  </div>
                  <div class="carousel-inner">
                  <div class="carousel-item active carousel-item-discount">';
                        for ($i = 1; $i <= 5; $i++) {
                        if ($i % 5 == 1) {
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                        }
                        echo '<div class="col-9 col-md-4 col-xl-2">';
                        $row = $discounted_books->fetch_assoc();
                        // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                        $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                              echo '<div class="card w-75 mx-auto d-block mb-3 rounded-border">';
                              echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                              echo '<img src="' . $imagePath . '" class="card-img-top rounded-border p-3" style="height: 20rem;" alt="...">';
                                    echo "<div class=\"card-body\">";
                                          echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                          echo '<p class="author">'.$row["authorName"].'</p>';
                                          if($row["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> '.$row["discount"].'%</p>';
                                          }
                                          if($row["discount"] > 0){
                                                echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $row["filePrice"] . '$</span> ' .round($row["filePrice"] - $row["filePrice"] * $row["discount"] / 100, 2). '$</p>';
                                                echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $row["physicalPrice"] . '$</span> ' .round($row["physicalPrice"] - $row["physicalPrice"] * $row["discount"] / 100, 2). '$</p>';
                                                }
                                                else {
                                                echo "<p class=\"price \">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                echo "<p class=\"price \">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                          }
                                          echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                          echo "(".$row["star"].")";
                                          
                                    echo "</div>";
                              echo "</a>";
                              echo "</div>";

                        echo '</div>';
                        if ($i % 5 == 0 || $i == $discounted_books->num_rows) {
                              echo '</div>';
                        }
                        }
                  
                  echo'</div>
                  <div class="carousel-item carousel-item-discount">';
           for ($i = 6; $i <= 10; $i++) {
                        if ($i % 5 == 1) {
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                        }
                        echo '<div class="col-9 col-md-4 col-xl-2">';
                        $row = $discounted_books->fetch_assoc();
                        // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                        $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                              echo '<div class="card w-75 mx-auto d-block mb-3 rounded-border">';
                              echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                              echo '<img src="' . $imagePath . '" class="card-img-top rounded-border p-3" style="height: 20rem;" alt="...">';
                                    echo "<div class=\"card-body\">";
                                          echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                          echo '<p class="author">'.$row["authorName"].'</p>';
                                          if($row["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> '.$row["discount"].'%</p>';
                                          }
                                          
                                          if($row["discount"] > 0){
                                                echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $row["filePrice"] . '$</span> ' .round($row["filePrice"] - $row["filePrice"] * $row["discount"] / 100, 2). '$</p>';
                                                echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $row["physicalPrice"] . '$</span> ' .round($row["physicalPrice"] - $row["physicalPrice"] * $row["discount"] / 100, 2). '$</p>';
                                                }
                                                else {
                                                echo "<p class=\"price \">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                echo "<p class=\"price \">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                          }
                                          echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                          echo "(".$row["star"].")";
                                          
                                    echo "</div>";
                              echo "</a>";
                              echo "</div>";

                        echo '</div>';
                        if ($i % 5 == 0 || $i == $discounted_books->num_rows) {
                              echo '</div>';
                        }
                        }
                  echo' </div>
                  
                  </div>
                  <button class="carousel-control-prev carousel-control-prev-discount" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon carousel-control-prev-icon-discount" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next carousel-control-next-discount" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                  <span class="carousel-control-next-icon carousel-control-next-icon-discount" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                  </button>
                  </div>';
                        echo '</div>';//container end
             echo '</div>';//div end
      ?>
      <!-- brows books section -->
      <?php
            echo '<div class="container-fluid w-75 border border-dark rounded-border bgr-col my-3">';
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
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                        }
                        echo '<div class="col-9 col-md-6 col-xl-4">';
                        $row = $elem->fetch_assoc();
                        // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                        $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                              echo '<div class="card w-75 mx-auto d-block mb-3">';
                              echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                              echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                                    echo "<div class=\"card-body\">";
                                          echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                          echo '<p class="author">'.$row["authorName"].'</p>';
                                          if($row["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                  <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                  <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                  <g id="SVGRepo_iconCarrier">
                        <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
            </svg> '.$row["discount"].'%</p>';
                                          }
                                          if($row["discount"] > 0){
                                                echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $row["filePrice"] . '$</span> ' .round($row["filePrice"] - $row["filePrice"] * $row["discount"] / 100, 2). '$</p>';
                                                echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $row["physicalPrice"] . '$</span> ' .round($row["physicalPrice"] - $row["physicalPrice"] * $row["discount"] / 100, 2). '$</p>';
                                                }
                                                else {
                                                echo "<p class=\"price \">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                echo "<p class=\"price \">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                          }
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