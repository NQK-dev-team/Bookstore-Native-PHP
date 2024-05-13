<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';

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
            require_once __DIR__ . '/../../../tool/php/check_https.php';

            try {
                  $bookID = sanitize(rawurldecode($_GET['id']));

                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  if (!$conn) {
                        http_response_code(500);
                        require_once __DIR__ . '/../../../error/500.php';
                        exit;
                  }

                  $stmt = $conn->prepare('select * from book where id=? and status=true');
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
                              $imagePath = (isSecure() ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($book['imagePath']));
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
                  $bDiscount=0;
                  if($result->num_rows===1)
                  {
                    $discount = $result->fetch_assoc();
                    $bDiscount = $discount['discount'];
                  }
                  $stmt->close();

                  $userStar = 'null';
                  $userComment = '';

                  if (check_session() && $_SESSION['type'] === 'customer') {
                        $canRate = false;

                        $stmt = $conn->prepare("SELECT(
                        exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=? and customerOrder.customerID=?) 
                        or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=? and customerOrder.customerID=?)
                        ) as result");

                        if (!$stmt) {
                              http_response_code(500);
                              require_once __DIR__ . '/../../../error/500.php';
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ssss', $bookID, $_SESSION['id'], $bookID, $_SESSION['id']);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              require_once __DIR__ . '/../../../error/500.php';
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        $canRate = $result['result'];
                        $stmt->close();

                        $stmt = $conn->prepare('select star,comment from rating where bookID=? and customerID=?');
                        if (!$stmt) {
                              http_response_code(500);
                              require_once __DIR__ . '/../../../error/500.php';
                              $conn->close();
                              exit;
                        }
                        $stmt->bind_param('ss', $bookID, $_SESSION['id']);
                        if (!$stmt->execute()) {
                              http_response_code(500);
                              require_once __DIR__ . '/../../../error/500.php';
                              $stmt->close();
                              $conn->close();
                              exit;
                        }
                        $result = $stmt->get_result();
                        if ($result->num_rows === 1) {
                              $result = $result->fetch_assoc();
                              $userStar = $result['star'];
                              $userComment = $result['comment'];
                        }
                        $stmt->close();
                  }

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
            <meta name="description" content="<?php echo $bDescription; ?>">
            <title><?php echo $bName; ?></title>
            <?php storeToken(); ?>
            <script>
                  const bookID = '<?php echo $bookID; ?>';
                  let originalRating = <?php echo $userStar; ?>;
                  let originalComment = '<?php echo $userComment; ?>';
            </script>
            <?php
            require_once __DIR__ . '/../../../head_element/google_analytic.php';
            ?>
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
                                    <div id='bookAvgRating'>
                                          <span class="text-warning fw-medium"><?php echo displayRatingStars($bStar); ?></span>
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

                  <div class="container bg-light rounded mt-3 mb-3 py-3">
                        <h4>Product Ratings <span id='totalRatings' class='fw-normal'></span></h4>
                        <div class='ratingPanel rounded d-flex align-items-md-center flex-md-row flex-column container-fluid p-3 mt-3'>
                              <div>
                                    <h6><span class='fs-4' id='avgRating'><?php echo $bStar; ?></span> out of 5</h6>
                                    <div class='fs-4 text-warning' id='avgRatingPanel'>
                                          <?php echo displayRatingStars($bStar); ?>
                                    </div>
                              </div>
                              <div class='ms-md-5 mt-3 mt-md-0'>
                                    <div class='d-none d-md-flex align-items-center'>
                                          <input onchange="setRatingFilter(event,'all')" type="radio" class="btn-check" name="ratingFilter" id="allStar" autocomplete="off" checked>
                                          <label class="btn border border-1 bg-white me-1 ratingFilter" for="allStar">All</label>

                                          <input onchange="setRatingFilter(event,'1')" type="radio" class="btn-check" name="ratingFilter" id="1Star" autocomplete="off">
                                          <label class="btn border border-1 bg-white mx-1 ratingFilter" for="1Star">1 Star</label>

                                          <input onchange="setRatingFilter(event,'2')" type="radio" class="btn-check" name="ratingFilter" id="2Star" autocomplete="off">
                                          <label class="btn border border-1 bg-white mx-1 ratingFilter" for="2Star">2 Stars</label>

                                          <input onchange="setRatingFilter(event,'3')" type="radio" class="btn-check" name="ratingFilter" id="3Star" autocomplete="off">
                                          <label class="btn border border-1 bg-white mx-1 ratingFilter" for="3Star">3 Stars</label>

                                          <input onchange="setRatingFilter(event,'4')" type="radio" class="btn-check" name="ratingFilter" id="4Star" autocomplete="off">
                                          <label class="btn border border-1 bg-white mx-1 ratingFilter" for="4Star">4 Stars</label>

                                          <input onchange="setRatingFilter(event,'5')" type="radio" class="btn-check" name="ratingFilter" id="5Star" autocomplete="off">
                                          <label class="btn border border-1 bg-white ms-1 ratingFilter" for="5Star">5 Stars</label>
                                    </div>
                                    <div class='d-flex d-md-none d-flex align-items-center'>
                                          <p class='mb-0'>Select</p>
                                          <select onchange="selectRatingFilter(event)" class="form-select pointer border-1 ms-3" aria-label="Select rating start">
                                                <option value="all" selected>All</option>
                                                <option value="1">1 Star</option>
                                                <option value="2">2 Stars</option>
                                                <option value="3">3 Stars</option>
                                                <option value="4">4 Stars</option>
                                                <option value="5">5 Stars</option>
                                          </select>
                                    </div>
                              </div>
                        </div>
                        <?php
                        if (check_session() && $_SESSION['type'] === 'customer' && $canRate) {
                        ?>
                              <hr>
                              <form class='d-flex flex-column' id='ratingForm'>
                                    <div class='d-flex align-items-center px-1'>
                                          <p class='mb-0'>Rating</p>
                                          <div class="star-rating">
                                                <input type="radio" id="5-stars" name="rating" value="5" onchange="setRating(event,5)" />
                                                <label for="5-stars" class="star">&#9733;</label>
                                                <input type="radio" id="4-stars" name="rating" value="4" onchange="setRating(event,4)" />
                                                <label for="4-stars" class="star">&#9733;</label>
                                                <input type="radio" id="3-stars" name="rating" value="3" onchange="setRating(event,3)" />
                                                <label for="3-stars" class="star">&#9733;</label>
                                                <input type="radio" id="2-stars" name="rating" value="2" onchange="setRating(event,2)" />
                                                <label for="2-stars" class="star">&#9733;</label>
                                                <input type="radio" id="1-star" name="rating" value="1" onchange="setRating(event,1)" />
                                                <label for="1-star" class="star">&#9733;</label>
                                          </div>
                                    </div>
                                    <textarea class='form-control mt-1' id='comment' placeholder='Comment about this product' rows=3></textarea>
                                    <div class='mt-3 ms-auto'>
                                          <button id='deleteBtn' class='btn btn-sm btn-danger <?php echo $userStar !== 'null' ? '' : 'none'; ?>' type='button' onclick="deleteRating()">Delete</button>
                                          <button class='btn btn-sm btn-secondary mx-1' type='button' onclick="resetRatingForm()">Reset</button>
                                          <button class='btn btn-sm btn-primary' type='submit'>Submit</button>
                                    </div>
                              </form>
                        <?php } ?>
                        <hr class='mb-0'>
                        <div class='d-flex flex-column'>
                              <h5 class='fw-normal mx-auto mt-3 none text-secondary' id='noRating'>This Product Has No Rating</h5>
                              <div id='ratingList'>
                              </div>
                              <div class='mx-auto mt-3' id='showBtn'>
                                    <button onclick='showAll=true; fetchRatings()' class='border-0 bg-light'>Show All <i class="bi bi-chevron-down"></i></button>
                              </div>
                        </div>
                  </div>

                  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel">
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
                  <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="modalLabel">
                        <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                    <div class="modal-header">
                                          <h2 class="modal-title fs-5">Notice</h2>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column">
                                          <p id='customMessage'></p>
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
            <script src="/tool/js/ratingStars.js"></script>
      </body>

      </html>

<?php } ?>
