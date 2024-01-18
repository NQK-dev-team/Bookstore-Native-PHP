<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../tool/php/role_check.php';

if (return_navigate_error() === 400) {
      http_response_code(400);
      require __DIR__ . '/../../error/400.php';
} else if (return_navigate_error() === 403) {
      http_response_code(403);
      require __DIR__ . '/../../error/403.php';
} else {
      require_once __DIR__. '/../../config/db_connection.php';
      require_once __DIR__. '/../../tool/php/converter.php';
      require_once __DIR__. '/../../tool/php/converter.php';

      try{
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../error/500.php';
                  exit;
            }

            $elem = $conn->prepare('select book.name, author.authorName from book inner join author on book.id = author.bookID');
            $elem->execute();
            $elem = $elem->get_result();
            $conn->close();
      }
      catch (Exception $e){
            http_response_code(500);
            require_once __DIR__ . '/../../error/500.php';
            exit;
      }
?>

      <!DOCTYPE html>
      <html>

      <head>
            <?php
            require __DIR__ . '/../../head_element/cdn.php';
            require __DIR__ . '/../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">

            <meta name="author" content="Quang Nguyen">
            <meta name="description" content="Home page of NQK bookstore">
            <title>NQK Shop</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div>
                              <?php
                                    if($elem->num_rows > 0){
                                          while($row=$elem->fetch_assoc()){
                                                echo "<div class=\"card dashboard-card\">";
                                                      echo "<div class=\"card-body\">";
                                                            echo "<h6>"."Book: ".$row["name"]."</h6>".$row["authorName"];
                                                      echo "</div>";
                                                echo "</div>";
                                          }
                                    }
                                    else{
                                          echo "Can't find the thing you need!";
                                    }
                              ?>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>