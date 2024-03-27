<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
    $result = $conn->query("WITH RankedBooks AS (
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
WHERE discount_rank = 1");



$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Return the books as a JSON response
echo json_encode($books);
?>