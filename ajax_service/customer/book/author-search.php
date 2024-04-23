<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$SearchedAuth = $_GET['SearchedAuth'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

if($SearchedAuth  !== "") {
    $SearchedAuth  = sanitize($SearchedAuth );
    $result = $conn->query("SELECT * FROM author WHERE author.authorName LIKE  '%$SearchedAuth%'");
}
else {
    $result = $conn->query("SELECT * FROM author LIMIT 5");
}

$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Return the books as a JSON response
echo json_encode($books);
?>