<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/comment.php';

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
                  //$bookID = $_GET['bookID'];
                  $bookID = $_GET['id'];
                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
                  $stmt = $conn->prepare('WITH RankedBooks AS (
  SELECT book.id, book.name,
         author.authorName,  book.edition,
            book.isbn,
            book.publisher,
            book.publishDate,
            book.description,
            book.imagePath,
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
                  $stmt->execute();
                  $result = $stmt->get_result();
                  // $book = $result->fetch_assoc();
                  
            } catch (Exception $e) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
            $stmt1 = $conn->prepare('select name,dob,address,phone,email,imagePath,gender from appUser join customer on appUser.id = customer.id where appUser.id = ?');
            if (!$stmt1) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }
            $stmt1->bind_param('s', $_SESSION['id']);
            $isSuccess = $stmt1->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt1->close();
                  $conn->close();
                  exit;
            }
            $result1 = $stmt1->get_result();
            if ($result->num_rows === 0) {
                  http_response_code(404);
                  require_once __DIR__ . '/../../../error/404.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $result1 = $result1->fetch_assoc();
?>

<?php
      date_default_timezone_set('Asia/Ho_Chi_Minh');
?>
      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <!-- <link rel="stylesheet" href="../../css/customer/book/book-detail.css"> -->
            <meta name="author" content="Anh Khoa">
            <meta name="description" content="Home page of NQK bookstore">
            <style>
                  .author {
                        color: gray;
                  }
                  .text-justify{
                        text-align: justify;
                  }
                  .comment-box{
                        margin-top: 20px;
                        padding: 20px;
                        border: 1px solid #e6e6e6;
                        border-radius: 5px;
                        background-color: hsl(0, 0%, 98%);
                        position: relative;
                  }
                  .comment-box p{
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 14px;
                        line-height: 16px;
                        color: #282828;
                        font-weight: 100;
                       
                  }
                  .edit-form {
                        position: absolute;
                        top: 0px;
                        right: 0px;
                  }
                  .edit-form button{
                        width: 40px;
                        color: #282828;
                        background-color: hsl(0, 0%, 98%);
                        border: none;
                        opacity: 0.7;
                  }
                  .edit-form button:hover{
                       opacity: 1;
                  }
                  .delete-form {
                        position: absolute;
                        top: 0px;
                        right: 60px;
                  }
                  .delete-form button{
                        width: 40px;
                        color: #282828;
                        background-color: hsl(0, 0%, 98%);
                        border: none;
                        opacity: 0.7;
                  }
                  .delete-form button:hover{
                       opacity: 1;
                  }
            </style>
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
                                    <div class="col-11 col-md-5 d-flex justify-content-center align-items-center">';
                                    echo '<img src="' . $imagePath . '" class="card-img-top w-75 rounded" alt="..."> </div>';
                                    echo '<div class="col-11 col-md-7"> ';
                                    echo '<h2 class="display-4">' . $book['name'] . '</h2>';
                                    if($book["discount"] > 0){
                                                echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                      <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg> '.$book["discount"].'%</p>';
                                    }
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
                                    if($book["discount"] > 0){
                                          echo '<p class="price h4">E-book price: <span style="text-decoration: line-through;">' . $book["filePrice"] . '$</span> ' .round($book["filePrice"] - $book["filePrice"] * $book["discount"] / 100, 2). '$</p>';
                                          echo '<p class="price h4">Physical price: <span style="text-decoration: line-through;">' . $book["physicalPrice"] . '$</span> ' .round($book["physicalPrice"] - $book["physicalPrice"] * $book["discount"] / 100, 2). '$</p>';
                                          }
                                          else {
                                          echo "<p class=\"price h4\">"."E-book price: ".$book["filePrice"]."$"."</p>";
                                          echo "<p class=\"price h4\">"."Physical price: ".$book["physicalPrice"]."$"."</p>";
                                          }
                                    echo '<span class="text-warning">'.displayRatingStars($book['star']).'</span>';
                                                           echo "(".$book['star'].")";
                                    echo '<p class="h5 mt-3">ISBN: ' . $book['isbn'] . '</p>';
                                    echo '<p class="h5 author">Author: ' . $book['authorName'] . '</p>';
                                    echo '<p class="h5">Publisher: ' . $book['publisher'] . '</p>';
                                    echo '<p class="h5">Publish date: ' . $book['publishDate'] . '</p>';
                                  echo '<a
                                          name=""
                                          id="add_to_cart"
                                          class="btn btn-primary text-light col-12 col-md-4 col-xxl-3 mt-3"
                                          href="#"
                                          role="button"
                                          data-book-id="' . $book['id'] . '"
                                          data-user-id="' . $_SESSION['id'] . '"
                                          >Add Digital Copy</a>';
                                    echo '<p class="h5 mt-4 ">Amount of physical copy to buy: </p>';
                                    echo '<div class="col-12 col-md-4 col-xxl-3 mt-3">
                                          <input
                                                type="number"
                                                id="quantity"
                                                min="1"
                                                value="1"
                                                class="form-control mt-1"
                                          >
                                          </div>
                                          <a
                                          name=""
                                          id="add_to_cart_physical"
                                          class="btn btn-primary text-light col-12 col-md-4 col-xxl-3 mt-3"
                                          href="#"
                                          role="button"
                                          data-book-id="' . $book['id'] . '"
                                          data-user-id="' . $_SESSION['id'] . '"
                                          >Add Physical Copies</a>';

                                    echo '</div>';
                              echo'</div>';

                              

                              echo '<div class="row justify-content-center align-items-center g-2 mt-3">';
                                    echo '<div class="col-11"> ';
                                    echo '<p class="h5">Description: </p>';
                                    echo '<p class="h6 text-justify">' . $book['description'] . '</p>';
                                    echo'</div>';
                              echo'</div>';

                              echo '<hr>';//break to separate book detail and comment section
                              //comment section
                              if(isset($_SESSION['id'])){
                              echo '<form method="POST" class="comment-input" action="'.setComment($conn, $bookID).'">
                                          <input type="hidden" name="customerID" value="'.$_SESSION['id'].'">
                                          <input type="hidden" name="commentTime" value="'.date('Y-m-d H:i:s').'">
                                          
                                          <input type="hidden" name="bookID" value="'.$bookID.'">
                                          <section style="background-color: #eee;">
                                                <div class="container my-5 py-5">
                                                      <div class="row d-flex justify-content-center">
                                                            <div class="col-md-12 col-lg-10 col-xl-8">
                                                                  <div class="card">
                                                                        <div class="card-footer py-3 border-0" style="background-color: #f8f9fa;">
                                                                              <div class="d-flex flex-start w-100">

                                                                                    <div class="form-outline w-100">
                                                                                          <textarea name="content" class="form-control" id="textAreaExample1" rows="4" style="background: #fff;"></textarea>
                                                                                          <label class="form-label" for="textAreaExample1">Message</label>
                                                                                    </div>
                                                                              </div>
                                                                              <div class="float-end mt-2 pt-1">
                                                                                    <button type="submit" name="commentSubmit" class="btn btn-primary btn-sm">Post comment</button>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </section>
                                    </form>';
                              }
                        getComment($conn, $bookID);
                        //comment section ends
                        //var_dump($_SESSION);
                        echo '</div>'; 
                  }
                  
            }
            
                  ?>
            </section>
            
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/javascript/customer/book/book-detail.js"></script>
      </body>

      </html>

<?php } ?>