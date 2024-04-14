<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';


$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

// Check if the POST parameters are set
if(isset($_POST['rating']) && isset($_POST['book_id']) && isset($_POST['user_id'])) {
    // Sanitize the POST parameters
    $rating = sanitize($_POST['rating']);
    $bookId = sanitize($_POST['book_id']);
    $userId = sanitize($_POST['user_id']);

    // Prepare the SQL statement
    //$stmt = $conn->prepare("INSERT INTO rating (book, customerID, star) VALUES (?, ?, ?)");
    $stmt = $conn->prepare("INSERT INTO rating (bookID, customerID, star) VALUES ('$bookId', '$userId', '$rating')");

    // Bind the parameters to the SQL statement
    //$stmt->bind_param("iii", $bookId, $userId, $rating);

    // Execute the SQL statement
    if($stmt->execute()) {
        echo "Rating saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Error: Missing POST parameters.";
}
?>