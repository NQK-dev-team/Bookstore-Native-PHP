<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$bookID = $_POST['book_id'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
// Query the database for books in the selected category
// This is a simplified example, you should use prepared statements to prevent SQL injection
//$result = $conn->query("SELECT category.id FROM category WHERE category.id = '$category'");


$result2 = $conn->query("SELECT * from physicalCopy where id='$bookID'");
$bookInStock = $result2->fetch_assoc();

// Return the books as a JSON response
echo json_encode($bookInStock['inStock']);
?>