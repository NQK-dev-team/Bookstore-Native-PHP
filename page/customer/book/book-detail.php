<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/comment.php';
require_once __DIR__ . '/../../../ajax_service/customer/book/rating.php';


$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
      if (isset($_GET['id'])) {

            require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
            require_once __DIR__ . '/../../../config/db_connection.php';
            require_once __DIR__ . '/../../../tool/php/converter.php';
            require_once __DIR__ . '/../../../tool/php/formatter.php';
            require_once __DIR__ . '/../../../tool/php/sanitizer.php';
            require_once __DIR__ . '/../../../tool/php/ratingStars.php';
            require_once __DIR__ . '/../../../tool/php/comment.php';

            try {
                  $bookID = sanitize(rawurldecode($_GET['id']));

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  if (!$conn) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        exit;
                  }

                  $stmt = $conn->prepare('select * from book where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  if ($result->num_rows === 0) {
                        http_response_code(404);
                        require_once __DIR__ . '/../../../error/404.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  while ($book = $result->fetch_assoc()) {
                        if ($bookID == $book['id']) {
                              $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($book['imagePath']));
                              $bName = $book['name'];
                              $bEdition = convertToOrdinal($book['edition']);
                              $bISBN = $book['isbn'];
                              $bPublisher = $book['publisher'];
                              $bPublishDate = MDYDateFormat($book['publishDate']);
                              $bDescription = $book['description'];
                              $bStar = $book['avgRating'];
                        }
                  }
                  $stmt->close();

                  $stmt = $conn->prepare('select authorName from author where bookID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $bAuthor = '';
                  while ($author = $result->fetch_assoc()) {
                        $bAuthor .= $author['authorName'] . ', ';
                  }
                  $bAuthor = rtrim($bAuthor, ', ');
                  $stmt->close();

                  $stmt = $conn->prepare('select category.name from belong join category on belong.categoryID = category.id where belong.bookID=?');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $bCategory = '';
                  while ($category = $result->fetch_assoc()) {
                        $bCategory .= $category['name'] . ', ';
                  }
                  $bCategory = rtrim($bCategory, ', ');
                  $stmt->close();

                  $stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $physical = $result->fetch_assoc();
                  $bPhysicalPrice = $physical['price'];
                  $bInStock = $physical['inStock'];
                  $physicalUnavailable = false;
                  if (!$bPhysicalPrice)
                        $physicalUnavailable = true;
                  $stmt->close();

                  $stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $file = $result->fetch_assoc();
                  $bFilePrice = $file['price'];
                  $bFilePath = $file['filePath'];
                  $fileUnavailable = false;
                  if (!$bFilePrice || !$bFilePath)
                        $fileUnavailable = true;
                  $stmt->close();

                  $stmt = $conn->prepare('select combined.discount from (
						select distinct discount.id,eventDiscount.discount,1 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct discount.id,eventDiscount.discount,2 as cardinal from eventDiscount join discount on discount.id=eventDiscount.id and discount.status=true join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=?
                    ) as combined order by combined.discount desc,combined.cardinal,combined.id limit 1');
                  if (!$stmt) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('s', $bookID);
                  if (!$stmt->execute()) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        $stmt->close();
                        $conn->close();
                        exit;
                  }
                  $result = $stmt->get_result();
                  $discount = $result->fetch_assoc();
                  $bDiscount = $discount['discount'];
                  $stmt->close();

                  $conn->close();
            } catch (Exception $e) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
      } else {
            http_response_code(400);
            require_once __DIR__ . '/../../../error/400.php';
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
            <link rel="stylesheet" href="/css/customer/book/book-detail.css">
            <meta name="page creator" content="Anh Khoa, Nghia Duong">
            <meta name="book author" content="<?php echo $bAuthor; ?>">
            <meta name="book name" content="<?php echo $bName; ?>">
            <meta name="description" content="<?php echo $bDescription; ?>">\
            <style>
                  .author {
                        color: gray;
                  }
                  .text-justify{
                        text-align: justify;
                  }
                  .comment-box{
                        padding: 20px;
                        border-bottom: 2px solid #999999;
                        /* border-radius: 5px; */
                        position: relative;
                        background-color: white;
                  }
                  .comment-box p{
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 14px;
                        line-height: 16px;
                        color: #282828;
                        font-weight: 100;
                       
                  }
                  .delete-form {
                        position: absolute;
                        top: 20px;
                        right: 60px;
                  }
                  .delete-form button{
                        width: 40px;
                        color: red;
                        font-size: 18px;
                        background-color: hsl(0, 0%, 98%);
                        border: none;
                        opacity: 0.7;
                  }
                  .delete-form button:hover{
                       opacity: 1;
                  }
                  .rating .bi {
                        font-size: 1em;
                        color: gray;
                        cursor: pointer;
                  }

                  .rating .bi.bi-star-fill {
                        color: gold;
                  }
                  .rating1 .bi {
                        font-size: 1em;
                        color: gray;
                        cursor: pointer;
                  }

                  .rating1 .bi.bi-star-fill {
                        color: gold;
                  }
                  .round{
                        border-radius: 20px;
                  }
                  /* Hide the radio buttons */
                  input[type="radio"] {
                        
                  }

                  /* Style the labels */
                  .btn-outline-primary {
                  transition: box-shadow .3s ease;
                  }
                  .Orange {
                  color: black;
                  }
                  .btn-outline-danger {
                  --bs-btn-color: #dc3545;
                  --bs-btn-border-color: black;
                  --bs-btn-hover-color: #b8b6b6;
                  }
                  .btn-check:checked+.btn{
                        color: #f70000;
                        background-color: #fff;
                        border-color: #ff5800;
                  }
                        
                  @media (max-width: 576px) { 
                        .img-size{
                              width: 220px;
                        }
                  }
                  @media (min-width: 576px) { 
                        .img-size{
                              width: 220px;
                        }
                  }

                  /* // Medium devices (tablets, 768px and up) */
                  @media (min-width: 768px) {
                        .img-size{
                              width: 250px;
                        }
                  }

                  /* // Large devices (desktops, 992px and up) */
                  @media (min-width: 992px) { 
                        .img-size{
                              width: 300px;
                        }
                  }
                  .btn-equal-width {
                  width: 40px;  /* Adjust this value as needed */
                  }
                  
            </style>
            <title><?php echo $bName; ?></title>
            <?php storeToken(); ?>
            <script>
                  const bookID = '<?php echo $bookID; ?>';
            </script>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page" class='position-relative'>
                  <div class="toast-container position-absolute top-0 end-0 p-3 h-100 overflow-y-auto hideBrowserScrollbar" id='toasts_container'>
                  </div>
                  <div class="container bg-light rounded p-3 mt-3">
                        <div class="row justify-content-center align-items-center">
                              <div class="col-12 col-md-5 d-flex flex-column justify-content-center align-items-center">
                                    <img src="<?php echo $imagePath; ?>" class="rounded img-size" alt="<?php echo $bName; ?>">
                              </div>
                              <div class="col-12 col-md-7 mt-md-0 mt-3">
                                    <h1><?php echo $bName; ?></h1>
                                    <p class="fw-medium"><?php echo $bEdition; ?> edition</p>
                                    <p>ISBN: <?php echo $bISBN; ?></p>
                                    <p>Author: <?php echo $bAuthor; ?></p>
                                    <p>Category: <?php echo $bCategory; ?></p>
                                    <p>Publisher: <?php echo $bPublisher; ?></p>
                                    <p>Publish date: <?php echo $bPublishDate; ?></p>
                                    <div>
                                          <span class="text-warning fw-medium" id="avg-rating"><?php echo displayRatingStars($bStar); ?></span>
                                          <?php echo  ' (' . $bStar . ')'; ?>
                                    </div>

                                    <form class='mt-3' id='addToCartForm'>
                                          <div>
                                                <?php if (!$physicalUnavailable) { ?>
                                                      <input type="radio" id="hardcover" name="bookType" class="btn-check" autocomplete="off">
                                                      <label class="btn border border-1 border-dark" for="hardcover">Hardcover</label>
                                                <?php } ?>

                                                <?php if (!$fileUnavailable) { ?>
                                                      <input type="radio" class="btn-check" name="bookType" id="ebook" autocomplete="off">
                                                      <label class="btn border border-1 border-dark <?php if (!$physicalUnavailable) echo 'ms-3'; ?>" for="ebook">E-book</label>
                                                <?php } ?>
                                          </div>

                                          <?php if (!$fileUnavailable) { ?>
                                                <h5 class='mt-3 none align-items-center mb-0' id='ebookPrice'>
                                                      <span class='fw-normal'>Price:</span>&nbsp;&nbsp;<span class='<?php if ($bDiscount) echo 'text-decoration-line-through'; ?>'>$<?php echo $bFilePrice; ?></span><?php if ($bDiscount) {
                                                                                                                                                                                                                              $discountedFilePrice = round($bFilePrice * (100.0 - $bDiscount) / 100, 2);
                                                                                                                                                                                                                              echo "&nbsp;&nbsp;<span>\${$discountedFilePrice}</span>&nbsp;&nbsp;<svg width=\"32px\" height=\"32px\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\" stroke=\"#ff0000\">
                                                <g id=\"SVGRepo_bgCarrier\" stroke-width=\"0\"></g>
                                                <g id=\"SVGRepo_tracerCarrier\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></g>
                                                <g id=\"SVGRepo_iconCarrier\">
                                                      <path d=\"M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z\" stroke=\"#ff0000\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M9 15L15 9\" stroke=\"#ff0000\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M14.4945 14.5H14.5035\" stroke=\"#ff0000\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M9.49451 9.5H9.50349\" stroke=\"#ff0000\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                </g>
                                          </svg><span class='text-danger'>{$bDiscount}%</span>";
                                                                                                                                                                                                                        } ?>
                                                </h5>
                                          <?php } ?>


                                          <?php if (!$physicalUnavailable) { ?>
                                                <h5 class='mt-3 none align-items-center mb-0' id='hardcoverPrice'>
                                                      <span class='fw-normal'>Price:</span>&nbsp;&nbsp;<span class='<?php if ($bDiscount) echo 'text-decoration-line-through'; ?>'>$<?php echo $bPhysicalPrice; ?></span><?php if ($bDiscount) {
                                                                                                                                                                                                                                    $discountedPhysicalPrice = round($bPhysicalPrice * (100.0 - $bDiscount) / 100, 2);
                                                                                                                                                                                                                                    echo "&nbsp;&nbsp;<span>\${$discountedPhysicalPrice}</span>&nbsp;&nbsp;<svg width=\"32px\" height=\"32px\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\" stroke=\"#ff0000\">
                                                <g id=\"SVGRepo_bgCarrier\" stroke-width=\"0\"></g>
                                                <g id=\"SVGRepo_tracerCarrier\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></g>
                                                <g id=\"SVGRepo_iconCarrier\">
                                                      <path d=\"M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z\" stroke=\"#ff0000\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M9 15L15 9\" stroke=\"#ff0000\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M14.4945 14.5H14.5035\" stroke=\"#ff0000\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                      <path d=\"M9.49451 9.5H9.50349\" stroke=\"#ff0000\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>
                                                </g>
                                          </svg><span class='text-danger'>{$bDiscount}%</span>";
                                                                                                                                                                                                                              } ?>
                                                </h5>
                                          <?php } ?>


                                          <?php if (!$physicalUnavailable) { ?>
                                                <div class='mt-3 align-items-center none' id='amountDisplayer'>
                                                      <div class="btn-group" role="group">
                                                            <input aria-label='Decrease amount' onclick='adjustAmount(false)' type="button" class="btn-check" id="decrease_book_ammount" autocomplete="off">
                                                            <label class="btn btn-sm btn-secondary" for="decrease_book_ammount">-</label>

                                                            <input onchange='checkAmmount()' type="number" class="fw-bold ammount_input ps-2 border border-2 border-secondary" id="book_ammount" autocomplete="off" value="<?php if ($bInStock) echo 1;
                                                                                                                                                                                                                              else echo 0; ?>" <?php if ($bInStock) echo 'min="1"'; ?> max="<?php echo $bInStock ?>">

                                                            <input aria-label='Increase amount' onclick='adjustAmount(true)' type="button" class="btn-check" id="increase_book_ammount" autocomplete="off">
                                                            <label class="btn btn-sm btn-secondary" for="increase_book_ammount">+</label>
                                                      </div>
                                                      <p class='mb-0 ms-3'>In stock: <strong id='in_stock'><?php echo $bInStock ?></strong></p>
                                                </div>
                                          <?php } ?>

                                          <?php
                                          if (!check_session())
                                                echo "<a href='/authentication/' class='btn btn-primary mt-3'>
                                                <i class='bi bi-cart4'></i>
                                                Add to cart
                                          </a>";
                                          else if (check_session() && $_SESSION['type'] === 'customer')
                                                echo "<button type='submit' class='btn btn-primary mt-3' disabled id='addToCartBtn'>
                                                <i class='bi bi-cart4'></i>
                                                Add to cart
                                          </button>";
                                          ?>
                                    </form>
                              </div>
                        </div>
                        <div class='mt-4'>
                              <h5>Description: </h5>
                              <p class="text-justify"><?php echo $bDescription; ?></p>
                        </div>
                  </div>

                  <div class="container bg-light rounded mt-2 mb-3">
                        <?php
                              $bookID = sanitize(rawurldecode($_GET['id']));

                              $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
            
                              //comment section
                              if(isset($_SESSION['id'])){
                                    echo '<form method="POST" class="comment-input" style="margin-block-end: 0em;" action="'.setComment($conn, $bookID).'">
                                                <input type="hidden" name="customerID" value="'.$_SESSION['id'].'">
                                                <input type="hidden" name="ratingTime" value="'.date('Y-m-d H:i:s').'">
                                                <input type="hidden" name="bookID" value="'.$bookID.'">
                                                <section style="background-color: white;">
                                                            <div class="row d-flex justify-content-center">    
                                                                        <div class="card round" style="background-color: white; border: none">
                                                                              <div class="card-footer py-3 border-0" style="background-color: white; border: none">
                                                                                    <div class="d-flex flex-start w-100">
                                                                                          <div class="form-outline w-100">
                                                                                                <textarea name="content" class="form-control" id="textAreaExample1" rows="4" style="background: #fff;"></textarea>
                                                                                                <label class="form-label" for="textAreaExample1" style="font-size: 20px">Message</label>
                                                                                          </div>
                                                                                    </div>
                                                                                    <div class="float-end mt-2 pt-1">
                                                                                          <button type="submit" name="commentSubmit" class="btn btn-primary btn-sm" style="font-size: 18px; padding: 10px 20px;">Post comment</button>
                                                                                    </div>
                                                                              </div>
                                                                        </div>      
                                                            </div>
                                                </section>
                                          </form>';
                                          echo '<div id="rating-container" style="margin-left:20px; background-color: white;">';
                                          echo ' <div class="rating" style="font-size:25px;">
                                                <span class="h6" style="font-size:25px;">Rate the book: </span>
                                                <i class="bi bi-star" data-value="1" data-book-id="'.$bookID.'" data-user-id="'. $_SESSION['id'].'"></i>
                                                <i class="bi bi-star" data-value="2" data-book-id="'.$bookID.'" data-user-id="'. $_SESSION['id'].'"></i>
                                                <i class="bi bi-star" data-value="3" data-book-id="'.$bookID.'" data-user-id="'. $_SESSION['id'].'"></i>
                                                <i class="bi bi-star" data-value="4" data-book-id="'.$bookID.'" data-user-id="'. $_SESSION['id'].'"></i>
                                                <i class="bi bi-star" data-value="5" data-book-id="'.$bookID.'" data-user-id="'. $_SESSION['id'].'"></i>
                                          </div>';
                                          echo '<div class="rating1" style="font-size:25px;" >
                                                <span class="h5" style="font-size:25px;">My rating: </span>
                                                <span id="rating-holder">'.GetRating($conn, $bookID, $_SESSION['id']).' </span>
                                                <div id="rating-response"></div>';
                                          echo '</div>'; 
                                          echo '</div>';
                                    }
                                    $sql = "SELECT * FROM rating WHERE bookID = '$bookID' LIMIT 5 ";
                                    $result = $conn->query($sql);
                                    $sql2 = "SELECT COUNT(*) as total_comments FROM rating WHERE bookID = '$bookID'";
                                    $result_new = $conn->query($sql2);
                                    $row2 = mysqli_fetch_assoc($result_new);
                                    $totalComments = $row2['total_comments'];
                                    echo '<div class="card-body text-center" style="background-color: white;">
                                    <h4 class="card-title" style="font-size: 40px;">Comments <span style="font-size: 20px;">(' . $totalComments . ' comments)</span></h4>';
                                    echo '</div>';
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<div class="comment-box"><p>';
                                            echo "<span style='font-weight: 600; font-size: 15px; color: black'>" . $row['customerID'] . "</span><br>";
                                            echo '<div class="rating1" >
                                                <span id="rating-holder">'.GetRating($conn, $bookID, $row['customerID']).' </span>
                                                <div id="rating-response"></div>';
                                            echo '</div>'; 
                                            echo '<span style=" opacity: 0.6; font-style: italic; font-size: 12px;">' . date('Y-m-d H:i', strtotime($row['ratingTime'])) . '</span><br><br>';
                                            echo nl2br($row['comment']."<br><br>");
                                        echo '</p>';
					if(isset($_SESSION['id'])){
                                        if($_SESSION['id'] == $row['customerID']){
                                            echo '<form class="delete-form" method="POST" action="'.deleteComments($conn).'">
                                            <input type="hidden" name="customerID" value="'.$row['customerID'].'">
                                            <input type="hidden" name="bookID" value="'.$bookID.'">
                                            <button type="submit" name="deleteComment" onclick="return confirm(\'Are you sure you want to delete this comment?\');">
                                                <i class="fas fa-trash-alt"></i> 
                                            </button>
                                            </form>';
                                        }}
                                        $book_id=$bookID;
                                        echo '</div>';
                                    }
                                    echo'<div class="collapse">';
                                                getComment($conn, $bookID);  
                                    echo'</div>';
      
                                    echo '<br><div style="text-align: center;">
                                          <button type="button" class="btn btn-primary" id="toggleButton" onclick="toggleButtonText()" style="width: 200px; height: 50px; font-size: 18px; padding: 10px;">Show all comments</button>';
                                    echo '</div>';
      
                        ?>
                  </div>

                  <div class=" modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Error</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p id="error_message"></p>
                                    </div>
                                    <div class="modal-footer">
                                          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                                    </div>
                              </div>
                        </div>
                  </div>
            </section>

            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/javascript/customer/book/book-detail.js"></script>
            <script src="/tool/js/input_validity.js"></script>
            <script src="/tool/js/encoder.js"></script>
      </body>

      </html>

<?php } ?>
