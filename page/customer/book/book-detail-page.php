<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../error/403.php';
} else {
            require_once __DIR__ . '/../../../config/db_connection.php';
            require_once __DIR__ . '/../../../tool/php/converter.php';
            require_once __DIR__ . '/../../../tool/php/formatter.php';
      
            try {
                  // Get book id from URL
                  $bookID = $_GET['bookID'];
                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
                  $stmt = $conn->prepare('SELECT DISTINCT 
            book.id,
            book.name,
            book.edition,
            book.isbn,
            book.ageRestriction,
            book.avgRating,
            book.publisher,
            book.publishDate,
            book.description,
            book.imagePath,
            author.authorName,
            filecopy.price
        FROM 
            book
        JOIN 
            author ON book.id = author.bookid
        JOIN 
            filecopy ON book.id = filecopy.id
        WHERE 
            book.status = 1
        ORDER BY 
            book.name, book.id');
                  $stmt->execute();
                  $result = $stmt->get_result();
                  // $book = $result->fetch_assoc();
                  
            } catch (Exception $e) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
?>

      <!DOCTYPE html>
      <html>

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Anh Khoa">
            <meta name="description" content="Home page of NQK bookstore">
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
                  echo 'Book ID: ' . $book['id'] . '<br>';
                  echo 'Name: ' . $book['name'] . '<br>';
                  echo 'Edition: ' . $book['edition'] . '<br>';
                  echo 'ISBN: ' . $book['isbn'] . '<br>';
                  echo 'Average Rating: ' . $book['avgRating'] . '<br><br>';
                  }
            }
                  ?>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>