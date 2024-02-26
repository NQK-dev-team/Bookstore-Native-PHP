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
        $sql2 = "INSERT INTO comment (customerID, bookID) VALUES ('$customerID', '$bookID')";
        $result2 = $conn->query($sql2);
        $sql = "INSERT INTO commentcontent (customerID, bookID, commentIdx, commentTime, content) VALUES ('$customerID', '$bookID', '$nextCommentIdx', '$commentTime', '$content')";
        $result = $conn->query($sql);
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
        echo '</p></div>';
    }
}