<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
    // Get the itemsPerPage and page parameters from the AJAX request
    $itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

    // Calculate the offset
    $offset = ($page - 1) * $itemsPerPage;

    // Connect to your database
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

    // Prepare the SQL query
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
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
WHERE discount_rank = 1 LIMIT ? OFFSET ?");

    // Bind the limit and offset parameters
    mysqli_stmt_bind_param($stmt, 'ii', $itemsPerPage, $offset);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the books
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Now you can output the books as HTML
    // This depends on how you want to display your books
    // Here's a basic example:
    // for ($i = 0; $i <= count($books); $i++) {
    //                     if ($i % 3 == 0) {
    //                           echo '<div class="row justify-content-center align-items-center g-2 m-3">';
    //                     }
    //                     echo '<div class="col-9 col-md-6 col-xl-4">';
    //                     //$books = $result->fetch_assoc();
    //                     // $books["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($books["pic"])) . "\"";
    //                     $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($books[$i]['pic']));
    //                           echo '<div class="card w-75 mx-auto d-block">';
    //                           echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($books[$i]["id"]))."\">"; 
    //                           echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
    //                                 echo "<div class=\"card-body\">";
    //                                       echo "<h5 class=\"card-title\">"."Book: ".$books[$i]["name"]."</h5>";
    //                                       if($books[$i]["discount"] > 0){
    //                                             echo '<p class="text-danger"> <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
    //               <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    //               <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    //               <g id="SVGRepo_iconCarrier">
    //                     <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    //                     <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    //                     <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
    //                     <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
    //               </g>
    //         </svg> '.$books[$i]["discount"].'%</p>';
    //                                       }
    //                                       echo '<p class="author">'.$books[$i]["authorName"].'</p>';
    //                                       if($books[$i]["discount"] > 0){
    //                                             echo '<p class="price ">E-book price: <span style="text-decoration: line-through;">' . $books[$i]["filePrice"] . '$</span> ' .round($books[$i]["filePrice"] - $books[$i]["filePrice"] * $books[$i]["discount"] / 100, 2). '$</p>';
    //                                             echo '<p class="price ">Physical price: <span style="text-decoration: line-through;">' . $books[$i]["physicalPrice"] . '$</span> ' .round($books[$i]["physicalPrice"] - $books[$i]["physicalPrice"] * $books[$i]["discount"] / 100, 2). '$</p>';
    //                                             }
    //                                             else {
    //                                             echo "<p class=\"price \">"."E-book price: ".$books[$i]["filePrice"]."$"."</p>";
    //                                             echo "<p class=\"price \">"."Physical price: ".$books[$i]["physicalPrice"]."$"."</p>";
    //                                       }
    //                                       echo '<span class="text-warning">'.displayRatingStars($books[$i]["star"]).'</span>';
    //                                       echo "(".$books[$i]["star"].")";
                                          
    //                                 echo "</div>";
    //                           echo "</a>";
    //                           echo "</div>";

    //                     echo '</div>';
    //                     if ($i % 3 == 2 || $i == count($books[$i])) {
    //                           echo '</div>';
    //                     }
    //                     }

    // foreach ($books as $book) {
    //     echo "<div class='book'>";
    //     echo "<h2>" . htmlspecialchars($book['name']) . "</h2>";
    //     echo "</div>";
    // }
    
    echo json_encode($books);
?>