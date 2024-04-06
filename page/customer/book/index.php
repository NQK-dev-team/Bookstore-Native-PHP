<?php
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
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
            $elem = '';

            $stmt = $conn->prepare('WITH RankedBooks AS (
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
WHERE discount_rank = 1');
            // $stmt = $conn->prepare('select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book inner join author on book.id = author.bookID
            // join fileCopy on book.id = fileCopy.id
            // join physicalCopy on book.id = physicalCopy.id');
            $stmt->execute();
            $result = $stmt->get_result();
            $cate = $conn->prepare('SELECT category.ID, category.name FROM category');
            $auth = $conn->prepare('SELECT author.authorName FROM author');
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
            <title>Book list</title>
            <style>
                  .card:hover {
                        transform: scale(1.1);
                  } 
                  .card {
                        margin: 1rem;
                  }
                  .author {
                        color: gray;
                  }
                  .pic {
                        height: 28rem;
                        width: 100%;
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
            #Discount_Button.on {
                  box-shadow: 0 0 10px #fff; /* White glow */
                  background-color: red; /* Faint white background */
                  color: #fff; /* White text */
            }
            #Best-Seller_Button.on {
                  box-shadow: 0 0 10px #fff; /* White glow */
                  background-color: #ffc107; /* Faint white background */
                  color: #fff; /* White text */
            }
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <h1 class="heading-decord" style="text-align: center;">Our collection</h1>

            <!--
            <ul id="book-List"
            <li>Example book</li>
            </ul>
            -->
      <div class="container">
                  <div class="row justify-content-center">
                        <div class="col-12 col-md-4 m-2">
                              <!-- category form -->
                               <select class="form-select " aria-label="Default select example" id="category">
                              <option selected value="All_Category">All Category</option>
                              <?php 
                                    if ($cate) {
                                          $success = $cate->execute();
                                          if ($success) {
                                                $result1 = $cate->get_result();
                                                while ($row = $result1->fetch_assoc()) {
                                                      // Process each row of data here...
                                                      echo '<option value="' . $row['ID'].'" >'. $row['name'] . '</option>';
                                                }
                                                      } else {
                                                echo "Error executing statement: " . $conn->error;
                                                      }     
                                                } else {
                                                echo "Error preparing statement: " . $conn->error;
                                                }
                              ?>
                              </select>  
                              <!-- end of catagory collum -->
                        </div>

                        <div class="col-12 col-md-4 m-2">
                              <!-- category form -->
                              <select class="form-select " aria-label="Default select example" id="DisplayBook">
                                    <option selected value="Default">Default Listing</option>
                                    <option value="Discount">Discount</option>
                                    <option value="Best-Seller">Best Seller</option>
                                    <option value="HighToLowPhysical">Physical Price Descending</option>
                                    <option value="LowToHighPhysical">Physical Price Ascending</option>
                              </select>  
                              <!-- end of select discount and best seller form -->
                        </div>
                        <!-- <button type="button" class="btn btn-outline-danger col-10 col-md-2 col-lg-1 m-2" id= "Discount_Button">Discount</button>
                        <button type="button" class="btn btn-outline-warning col-10 col-md-2 col-lg-1 m-2" id= "Best-Seller_Button">Best seller</button> -->
                        <!-- search bar -->
                  <div class="row justify-content-center">
                        <div class="col-12 col-md-5 m-2">
                              <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1" role="search" id="search_book">
                                    <input id="search-input" class="form-control me-2" type="search" placeholder="Search by name, author or ISBN number or Publisher" aria-label="Search">
                                    <!-- <input type="submit" value="Search" class="btn btn-primary"> -->
                                    <button type="submit" class="btn btn-primary">
                                          <i class="fas fa-search"></i>
                                    </button>
                              </form>
                        </div>
                  </div>
                  
                  <div class="row justify-content-center">
                        <div class="col-12 col-md-2 m-2">
                              <select class="form-select" id="itemsPerPage">
                                    <option value="100" selected>All books</option>
                                    <option value="6">6 books per page</option>
                                    <option value="12">12 books per page</option>
                                    <option value="24">24 books per page</option>
                                    <option value="51">51 books per page</option>
                              </select>
                        </div>
                        <nav class=" col-12 col-md-2 m-2 page-nav" aria-label="Page navigation example">
                              <ul class="pagination">
                                    <li class="page-item">
                                          <a class="page-link" href="#">«</a>
                                    </li>
                                    <!-- Add as many page links as you need -->
                                    <li class="page-item">
                                          <a class="page-link" href="#">»</a>
                                    </li>
                              </ul>
                        </nav> 
                        
                  </div>
                  
            </div>
            <br>
            <!-- <div id="TestBookList">
                  <p>Test Item perpage here</p>
            </div> -->
            <div id="bookList">
                  <?php
                        for ($i = 1; $i <= $result->num_rows; $i++) {
                        if ($i % 3 == 1) {
                              echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                        }
                        echo '<div class="col-11 col-md-6 col-xl-4">';
                        $row = $result->fetch_assoc();
                        // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                        $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                              echo '<div class="card w-75 mx-auto d-block">';
                              echo "<a href=\"book-detail?id=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                              echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                                    echo "<div class=\"card-body\">";
                                          echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
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
                                          echo '<p class="author">'.$row["authorName"].'</p>';
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
                        if ($i % 3 == 0 || $i == $result->num_rows) {
                              echo '</div>';
                        }
                        }
                        
                        
                  ?>
            </div>
      
      </div>
            
        
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
            <script src="/javascript/customer/book/book-list-cus.js"></script>
            
      </body>

      </html>

<?php } ?>