SELECT belong.bookID,  belong.categoryID, category.name FROM 
bookstore.belong inner join bookstore.category
ON belong.categoryID = category.id;

Select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic,  belong.categoryID, book.avgRating as star, category.name categoryNAME from 
bookstore.book inner join bookstore.author on book.id = author.bookID
            join bookstore.fileCopy on book.id = fileCopy.id
            join bookstore.physicalCopy on book.id = physicalCopy.id
            join bookstore.belong on book.id = belong.bookID
            join bookstore.category on belong.categoryID = category.id;
            -- Where category.id = "CATEGORY1";
            
 -- give back multi books but give multiple discount
Select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic,  book.avgRating as star, eventapply.eventID, eventdiscount.discount from 
bookstore.book inner join bookstore.author on book.id = author.bookID
            join bookstore.fileCopy on book.id = fileCopy.id
            join bookstore.physicalCopy on book.id = physicalCopy.id
            left join bookstore.eventapply on book.id = eventapply.bookID
            left join bookstore.eventdiscount on eventapply.eventID = eventdiscount.ID;
            
-- only books with discount remains for this query  --> use this for select discount books only 
            SELECT book.id, book.name, author.authorName, 
       fileCopy.price AS filePrice, 
       physicalCopy.price AS physicalPrice, 
       book.imagePath AS pic, 
       book.avgRating AS star, 
       eventapply.eventID, 
       MAX(eventdiscount.discount) AS Discount
FROM bookstore.book
INNER JOIN bookstore.author ON book.id = author.bookID
INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
GROUP BY book.id, book.name, author.authorName, fileCopy.price, physicalCopy.price, book.imagePath, book.avgRating, eventapply.eventID
HAVING MAX(eventdiscount.discount) IS NOT NULL OR MAX(eventdiscount.discount) = 0;

-- trying to give book with no discount and the hightest discount
Select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic,  book.avgRating as star, eventapply.eventID, COALESCE(eventdiscount.discount, 0) as Discount from 
bookstore.book inner join bookstore.author on book.id = author.bookID
            join bookstore.fileCopy on book.id = fileCopy.id
            join bookstore.physicalCopy on book.id = physicalCopy.id
            left join bookstore.eventapply on book.id = eventapply.bookID
            left join bookstore.eventdiscount on eventapply.eventID = eventdiscount.ID;
-- give book with no discount and the hightest discount
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
  FROM bookstore.book
  INNER JOIN bookstore.author ON book.id = author.bookID
  INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
  INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
  LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1;

-- specific catagory and get discount as well
with CatagorySelect as(
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
  FROM bookstore.book
  INNER JOIN bookstore.author ON book.id = author.bookID
  INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
  INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
  LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1)

SELECT *
FROM CatagorySelect  
	join bookstore.belong on CatagorySelect.id = belong.bookID
	join bookstore.category on belong.categoryID = category.id;
    
    
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
  FROM bookstore.book
  INNER JOIN bookstore.author ON book.id = author.bookID
  INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
  INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
  LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1 AND discount != 0;

-- Most detail sale query
 WITH RankedBooks AS (
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
  FROM bookstore.book
  left join (select sum(amount) as pSales, physicalOrderContain.bookID from bookstore.physicalOrderContain group by bookID) as physicalOrders on book.id = physicalOrders.bookID
right join (select count(orderID) as fSales, fileOrderContain.bookID from bookstore.fileOrderContain group by bookID) as fileOrders on book.id = fileOrders.bookID
  INNER JOIN bookstore.author ON book.id = author.bookID
  INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
  INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
  LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1;

SELECT *
FROM bookstore.book
WHERE book.name LIKE  '%FRANK%';

-- FUNCTIONAL search query
WITH SearchBooks AS (
WITH RankedBooks AS (
  SELECT book.id, book.name, book.isbn,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM bookstore.book
  INNER JOIN bookstore.author ON book.id = author.bookID
  INNER JOIN bookstore.fileCopy ON book.id = fileCopy.id
  INNER JOIN bookstore.physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN bookstore.eventapply ON book.id = eventapply.bookID
  LEFT JOIN bookstore.eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1
)
SELECT *
FROM SearchBooks
WHERE SearchBooks.name LIKE  '%FRANK%' or SearchBooks.authorName LIKE '%FRANK%' or SearchBooks.isbn LIKE '%FRANK%';