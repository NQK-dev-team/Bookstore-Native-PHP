<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
    // Get the itemsPerPage and page and Display parameters from the AJAX request
    $itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $Display = $_GET['Display'];
    $search = $_GET['query'];
    $selectedCategory = $_GET['selectedCategory'];
    $selectedAuthor = $_GET['selectedAuthor'];
    $selectedPub = $_GET['selectedPub'];

    // Calculate the offset
    $offset = ($page - 1) * $itemsPerPage;

    // Connect to your database
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

    // Prepare the SQL query
    if ($Display === "Default" && $search === "" && $selectedCategory === "") {
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
  }
  if ($Display === "Discount" && $search === "" && $selectedCategory === "") {
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
      WHERE discount_rank = 1 AND discount != 0 LIMIT ? OFFSET ?");
  }
  if ($Display === "Best-Seller" && $search === "" && $selectedCategory === "") {
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
    SELECT book.id, book.name,
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
  WHERE discount_rank = 1 LIMIT ? OFFSET ?");
}
if ($Display === "LowToHighPhysical" && $search === "" && $selectedCategory === "") {
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
WHERE discount_rank = 1 ORDER BY physicalPrice ASC LIMIT ? OFFSET ?");
}
if ($Display === "HighToLowPhysical" && $search === "" && $selectedCategory === "") {
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
WHERE discount_rank = 1 ORDER BY physicalPrice DESC LIMIT ? OFFSET ?");
}
if ($Display === "PublishDateDes" && $search === "" && $selectedCategory === "") {
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
  SELECT book.id, book.name, book.publishDate,
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
WHERE discount_rank = 1 ORDER BY publishDate DESC LIMIT ? OFFSET ?");
}
if ($Display === "PublishDateAsc" && $search === "" && $selectedCategory === "") {
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
  SELECT book.id, book.name, book.publishDate,
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
WHERE discount_rank = 1 ORDER BY publishDate ASC LIMIT ? OFFSET ?");
}

if ($search !== "") {
    $stmt = mysqli_prepare($conn, "WITH SearchBooks AS (
WITH RankedBooks AS (
  SELECT book.id, book.name, book.isbn, book.publisher,
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
WHERE discount_rank = 1
)
SELECT *
FROM SearchBooks
WHERE SearchBooks.name LIKE  '%$search%' or SearchBooks.authorName LIKE '%$search%' or SearchBooks.isbn LIKE '%$search%'or SearchBooks.publisher LIKE '%$search%' LIMIT ? OFFSET ?");
}
if ($search === "" && $selectedCategory !== "" && $selectedAuthor === "" && $selectedPub === "") {
    $stmt = mysqli_prepare($conn, "WITH CatagorySelect as(
WITH RankedBooks AS (
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
WHERE discount_rank = 1)

SELECT *
FROM CatagorySelect  
	join belong on CatagorySelect.id = belong.bookID
	join category on belong.categoryID = category.id
    Where category.name = '$selectedCategory' LIMIT ? OFFSET ?");
}
if ($search === "" && $selectedCategory === "" && $selectedAuthor !== "" && $selectedPub === "") {
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
WHERE discount_rank = 1 AND RankedBooks.authorName ='$selectedAuthor' LIMIT ? OFFSET ?");
}
if ($search === "" && $selectedCategory === "" && $selectedAuthor === "" && $selectedPub !== "") {
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
  SELECT book.id, book.name, book.publisher,
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
WHERE discount_rank = 1 AND RankedBooks.publisher ='$selectedPub' LIMIT ? OFFSET ?");
}
    // Bind the limit and offset parameters
    mysqli_stmt_bind_param($stmt, 'ii', $itemsPerPage, $offset);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the books
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    echo json_encode($books);
?>