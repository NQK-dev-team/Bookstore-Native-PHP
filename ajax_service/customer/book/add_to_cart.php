<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$bookId = $_POST['book_id'];
$userId = $_POST['user_id'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
// Query the database for books in the selected category
// This is a simplified example, you should use prepared statements to prevent SQL injection
//$result = $conn->query("SELECT category.id FROM category WHERE category.id = '$category'");
$sql1 = "SELECT * FROM customerorder WHERE customerorder.customerID = '$userId' and customerorder.status = 0;";
$result1 = $conn->query($sql1);
if($result1 === FALSE || mysqli_num_rows($result1) == 0){
  $sql = "SELECT CONCAT('ORDER', MAX(CAST(SUBSTRING(id, 6) AS UNSIGNED)) + 1) AS new_id FROM customerorder";
  $result = $conn->query($sql);
  $row1 = $result->fetch_assoc();
  $newId = $row1['new_id'];
  $sql3 = "INSERT INTO customerorder (id, status, totalCost, totalDiscount, customerID) VALUES ('$newId',0,0,0, '$userId')";
  $result3 = $conn->query($sql3);

  $sql4 = "INSERT INTO fileorder (id) VALUES ('$newId')";
  $result4 = $conn->query($sql4);

  $sql5 = "INSERT INTO fileordercontain (bookID, orderID) VALUES ('$bookId', '$newId')";
  $result5 = $conn->query($sql5);

  $sql6 = "INSERT INTO physicalorder (id, destinationAddress) VALUES ('$newId', '211 Ly Thuong Kiet')";
  $result6 = $conn->query($sql6);
}
if($result1 !== FALSE && mysqli_num_rows($result1) > 0){
  $row = $result1->fetch_assoc();
  $orderID = $row['id'];
  $sql7 = "SELECT * FROM fileorder";
  $result7 = $conn->query($sql7);
  
  $books = array();
  $found = false;
  while($row7 = $result7->fetch_assoc()){
     $books[] = $row7;
    
    if($row7['id'] == $orderID){
        $sql2 = "INSERT INTO fileordercontain (bookID, orderID) VALUES ('$bookId', '$orderID')";
        $result2 = $conn->query($sql2);
        $found = true;
        break;
    }
  }

  if(!$found){
    $sql8 = "INSERT INTO fileorder (id) VALUES ('$orderID')";
    $result8 = $conn->query($sql8);
    $sql2 = "INSERT INTO fileordercontain (bookID, orderID) VALUES ('$bookId', '$orderID')";
    $result2 = $conn->query($sql2);
  }
}
echo json_encode($books);
?>