<?php

use Dotenv\Parser\Value;

require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/comment.php';

$return_status_code = return_navigate_error();
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

?>

<?php
      date_default_timezone_set('Asia/Ho_Chi_Minh');
?>
      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <!-- <link rel="stylesheet" href="../../css/customer/book/book-detail.css"> -->
            <meta name="author" content="Anh Khoa">
            <meta name="description" content="edit comment">
            <style>
                  .comment-box{
                        margin-top: 20px;
                        padding: 20px;
                        border: 1px solid #e6e6e6;
                        border-radius: 5px;
                        background-color: hsl(0, 0%, 98%);
                        position: relative;
                  }
                  .comment-box p{
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 14px;
                        line-height: 16px;
                        color: #282828;
                        font-weight: 100;
                       
                  }
                  .edit-form {
                        position: absolute;
                        top: 0px;
                        right: 0px;
                  }
                  .edit-form button{
                        width: 40px;
                        color: #282828;
                        background-color: hsl(0, 0%, 98%);
                        border: none;
                        opacity: 0.7;
                  }
                  .edit-form button:hover{
                       opacity: 1;
                  }
            </style>
            <title>Edit-comment</title>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
                  <?php
                  $customerID = $_POST['customerID'];
                  $bookID = $_POST['bookID'];
                  $commentTime = $_POST['commentTime'];
                  $content = $_POST['content'];
                  $commentIdx = $_POST['commentIdx'];
                  
                  echo '<form method="POST" action="'.editComment($conn).'">
                                          <input type="hidden" name="customerID" value="'.$customerID.'">
                                          <input type="hidden" name="commentTime" value="'.date('Y-m-d H:i:s').'">
                                          
                                          <input type="hidden" name="bookID" value="'.$bookID.'">
                                          <input type="hidden" name="commentIdx" value="'.$commentIdx.'">
                                          <textarea name="content" >'.$content.'</textarea>
                                          <button type="submit" name="commentUpdate" onclick="alert(\'Comment updated!\')">Update</button>
                                          
                                          <a
                                          name="back-to-book"
                                          class="btn btn-info text-light"
                                          href="book-detail?bookID=' . $bookID . '"
                                          role="button"
                                          >Back to book</a>
                                          
                                    </form>';
                  ?>
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php ?>