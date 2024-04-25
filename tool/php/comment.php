<?php

function setComment($conn, $bookID) {
    if (isset($_POST['commentSubmit'])){
        // echo "Comment submitted";
        $customerID = $_POST['customerID'];
        $bookID = $_POST['bookID'];
        $ratingTime = $_POST['ratingTime'];
        $content = $_POST['content'];
        $alert = "<script>alert('You haven't rated this book yet.')</script>";
            echo $alert;
        $sql_check = "SELECT comment FROM rating WHERE customerID = '$customerID' AND bookID = '$bookID'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            $sql_update = "UPDATE rating SET ratingTime = '$ratingTime', comment = '$content' WHERE customerID = '$customerID' AND bookID = '$bookID'";
            $result_update = $conn->query($sql_update);
            
        } else {
            $alert = "<script>alert('You haven't rated this book yet.')</script>";
            echo $alert;
        }
        }
    }
function getComment($conn, $bookID) {
    $sql = "SELECT * FROM rating WHERE bookID = '$bookID' LIMIT 10000 OFFSET 5 ";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo '<div class="comment-box"><p>';
        echo "<span style='font-weight: 600; font-size: 15px; color: black'>" . $row['customerID'] . "</span>";
        echo '<div class="rating1" >
            <span id="rating-holder">'.GetRating($conn, $bookID, $_SESSION['id']).' </span>
            <div id="rating-response"></div>';
        echo '</div>';
        echo '<span style=" opacity: 0.6; font-style: italic; font-size: 12px;">' . date('Y-m-d H:i', strtotime($row['ratingTime'])) . '</span><br><br>';
        echo nl2br($row['comment']."<br><br>");
        echo '</p>';
        if($_SESSION['id'] == $row['customerID']){
        echo '
            <form class="delete-form" method="POST" action="'.deleteComments($conn).'">
            <input type="hidden" name="customerID" value="'.$row['customerID'].'">
            <input type="hidden" name="bookID" value="'.$bookID.'">
            <button type="submit" name="deleteComment" onclick="return confirm(\'Are you sure you want to delete this comment?\');">
            <i class="fas fa-trash-alt"></i> 
            </button>
            </form>';
        }
        echo '</div>';
        };
}

function editComment($conn) {
    if (isset($_POST['commentUpdate'])){
        // echo "Comment submitted";
        $customerID = $_POST['customerID'];
        $bookID = $_POST['bookID'];
        $commentTime = $_POST['commentTime'];
        $content = $_POST['content'];
        $commentIdx = $_POST['commentIdx'];

        $sql = "UPDATE commentcontent SET content = '$content', commentTime = '$commentTime' WHERE customerID = '$customerID' AND bookID = '$bookID' AND commentIdx = '$commentIdx'";
        $result = $conn->query($sql);
    }
}
function deleteComments($conn) {
    if (isset($_POST['deleteComment'])){
        // echo "Comment submitted";
        $customerID = $_POST['customerID']; 
        $bookID = $_POST['bookID'];

        $sql = "Delete FROM rating WHERE customerID = '$customerID' AND bookID = '$bookID'";
        $result = $conn->query($sql);
    }
}