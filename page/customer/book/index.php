<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else {
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

            $stmt = $conn->prepare('select distinct book.id,book.name,book.edition,book.isbn,book.ageRestriction,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath from book where book.status=1 order by book.name,book.id limit 10');
            $stmt->execute();
            $result = $stmt->get_result();

            echo '<section id="page">';
            while ($row = $result->fetch_assoc()) {
            echo '<div>';
            echo '<h2>' . $row['name'] . '</h2>';
            echo '<p>Edition: ' . $row['edition'] . '</p>';
            // Output other fields as needed...
            echo '</div>';
            }
            echo '</section>';
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

      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <p>Customer book page</p>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>