<?php

function setComment($conn, $bookID) {
    if (isset($_POST['commentSubmit'])){
        // echo "Comment submitted";
        $customerID = $_POST['customerID'];
        $bookID = $_POST['bookID'];
        $commentTime = $_POST['commentTime'];
        $content = $_POST['content'];
        // $commentIdx = $_POST['commentIdx'];

        //commentIDx
        $sql1 = "SELECT MAX(commentIdx) AS maxCommentIdx FROM commentcontent WHERE bookID = '$bookID'";
        $result1 = $conn->query($sql1);
        if ($result1->num_rows > 0) {
            $row = $result1->fetch_assoc();
            $nextCommentIdx = $row['maxCommentIdx'] + 1;
        } else {
            $nextCommentIdx = 1;
        }
        $result = $conn->query("SELECT * FROM comment");
        $found = false;
        while ($row = $result->fetch_assoc()) {
            if($row['customerID'] == $customerID && $row['bookID'] == $bookID){
                $sql3 = "INSERT INTO commentcontent (customerID, bookID, commentIdx, commentTime, content) VALUES ('$customerID', '$bookID', '$nextCommentIdx', '$commentTime', '$content')";
                $result3 = $conn->query($sql3);
                $found = true;
                break;
            }
        }

        if(!$found){
            $sql2 = "INSERT INTO comment (customerID, bookID) VALUES ('$customerID', '$bookID')";
            $result2 = $conn->query($sql2);
            $sql3 = "INSERT INTO commentcontent (customerID, bookID, commentIdx, commentTime, content) VALUES ('$customerID', '$bookID', '$nextCommentIdx', '$commentTime', '$content')";
            $result3 = $conn->query($sql3);
        }
    }
}
function getComment($conn, $bookID) {
    $sql = "SELECT * FROM commentcontent WHERE bookID = '$bookID'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo '<div class="comment-box"><p>';
            echo $row['customerID']."<br>";
            echo $row['commentTime']."<br>";
            echo nl2br($row['content']."<br><br>");
        echo '</p>';
        if($_SESSION['id'] == $row['customerID']){
            echo '<form class="edit-form" method="POST" action="edit-comment.php">
            <input type="hidden" name="customerID" value="'.$row['customerID'].'">
            <input type="hidden" name="commentIdx" value="'.$row['commentIdx'].'">
            <input type="hidden" name="commentTime" value="'.$row['commentTime'].'">
            <input type="hidden" name="bookID" value="'.$bookID.'">
            <input type="hidden" name="content" value="'.$row['content'].'">
            <button name="edit">Edit</button>
        </form>
        <form class="delete-form" method="POST" action="'.deleteComments($conn).'">
            <input type="hidden" name="customerID" value="'.$row['customerID'].'">
            <input type="hidden" name="commentIdx" value="'.$row['commentIdx'].'">
            <input type="hidden" name="bookID" value="'.$bookID.'">
            <button type="submit" name="deleteComment" onclick="if(confirm(\'Are you sure you want to delete this comment?\')) { this.form.submit(); location.reload(); } else {return false;}">Delete</button>
        </form>';
        }
        echo '</div>';
    }
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
        $commentIdx = $_POST['commentIdx'];

        $sql = "Delete FROM commentcontent WHERE customerID = '$customerID' AND bookID = '$bookID' AND commentIdx = '$commentIdx'";
        $result = $conn->query($sql);
    }
}