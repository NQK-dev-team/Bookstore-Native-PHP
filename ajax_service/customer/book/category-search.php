<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$SearchedCategory = $_GET['SearchedCategory'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

if($SearchedCategory !== "") {
    $SearchedCategory = sanitize($SearchedCategory);
    $result = $conn->query("SELECT * FROM bookstore.category WHERE category.name LIKE  '%$SearchedCategory%'");
}
else {
    $result = $conn->query("SELECT * FROM bookstore.category LIMIT 5");
}

$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Return the books as a JSON response
echo json_encode($books);
?>