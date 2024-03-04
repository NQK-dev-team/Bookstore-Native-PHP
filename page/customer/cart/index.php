<?php
require_once __DIR__ . '/../../../tool/php/login_check.php';
require_once __DIR__ . '/../../../tool/php/role_check.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else {
      require_once __DIR__ . '/../../../tool/php/anti_csrf.php';
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';

      try{
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }


            $stmt = $conn->prepare('select id from customerOrder where status = 0 and customerID = ?;');
            if (!$stmt) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }

            $stmt->bind_param('s', $_SESSION['id']);
            $isSuccess = $stmt->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $orderID = $stmt->get_result()->fetch_assoc()['id'];

            $stmt->close();

            $pBook = $conn->prepare('select bookID, imagePath, name, amount, price from physicalOrderContain
                                    join book on physicalOrderContain.bookID = book.id 
                                    join physicalCopy on book.id = physicalCopy.id
                                    where orderID = ?;');
            if (!$pBook) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }

            $pBook->bind_param('s', $orderID);
            $isSuccess = $pBook->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $pBook = $pBook->get_result();

            $fBook = $conn->prepare('select bookID, imagePath, name, price from fileOrderContain
                                    join book on fileOrderContain.bookID = book.id 
                                    join fileCopy on book.id = fileCopy.id
                                    where orderID = ?;');
            if (!$fBook) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $conn->close();
                  exit;
            }

            $fBook->bind_param('s', $orderID);
            $isSuccess = $fBook->execute();
            if (!$isSuccess) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  $stmt->close();
                  $conn->close();
                  exit;
            }
            $fBook = $fBook->get_result();



            // $result = $result->fetch_assoc();
            // $stmt->close();
            $pBook->close();
            $fBook->close();

            $conn->close();
      } catch (Exception $e) {
            http_response_code(500);
            require_once __DIR__ . '/../../../error/500.php';
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

            <meta name="author" content="Quang Nguyen">
            <meta name="description" content="Cart of a customer before checkout">
            <title>Cart</title>
            <?php storeToken(); ?>

      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <div>                      
                              <?php
                                    if($pBook->num_rows == 0 && $fBook->num_rows == 0){
                                          echo '<h2>Nothing in your cart</h2>';
                                    }
                                    
                                    if($pBook->num_rows > 0){
                                          echo '<h2>Physical copy</h2>';
                                          echo '<table class="table">';
                                                echo '<thead>';
                                                      echo '<tr>';
                                                      echo '<th scope="col">Image</th>';
                                                      echo '<th scope="col">Name</th>';
                                                      echo '<th scope="col">Amount</th>';
                                                      echo '<th scope="col">Price</th>';
                                                      echo '</tr>';
                                                echo '</thead>';

                                                echo '<tbody>';

                                          while($row = $pBook->fetch_assoc()){
                                                $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
                                                echo '<tr>';
                                                      echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 5rem;" alt="...">';
                                                      echo '<td class = "name">'.$row['name'].'</td>';
                                                      echo '<td class = "amount">'.$row['amount'].'</td>';
                                                      echo '<td class = "price">'.$row['price'].'</td>';
                                                echo '</tr>';
                                          }
                                                echo '</tbody>';
                                          echo '</table>';
                                    }

                                    if($fBook->num_rows > 0){
                                          echo '<h2>File copy</h2>';
                                          echo '<table class="table">';
                                                echo '<thead>';
                                                      echo '<tr>';
                                                      echo '<th scope="col">Image</th>';
                                                      echo '<th scope="col">Name</th>';
                                                      echo '<th scope="col">Price</th>';
                                                      echo '</tr>';
                                                echo '</thead>';

                                                echo '<tbody>';

                                          while($row = $fBook->fetch_assoc()){
                                                $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
                                                echo '<tr>';
                                                      echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 5rem;" alt="...">';
                                                      echo '<td class = "name">'.$row['name'].'</td>';
                                                      echo '<td class = "price">'.$row['price'].'</td>';
                                                echo '</tr>';
                                          }
                                                echo '</tbody>';
                                          echo '</table>';
                                    }
                              ?>
                  </div>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>