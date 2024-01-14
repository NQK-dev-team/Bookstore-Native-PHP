
<?php
require_once __DIR__ . '/../tool/php/session_check.php';
require_once __DIR__ . '/../tool/php/converter.php';

if (check_session()) {
      if ($_SESSION['type'] === 'admin') {
            $requestedUri = revertEncodedCharacter($_SERVER['REQUEST_URI']);
            $filePath = dirname(__DIR__) . $requestedUri;

            $contentType = mime_content_type($filePath);

            // Set the appropriate Content-Type header
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . filesize($filePath));

            // Fetch and output the content of the requested URI
            ob_clean();
            readfile($filePath);
            flush();
            exit;
      } else if ($_SESSION['type'] === 'customer') {
      }
} else {
      http_response_code(403);
      require_once __DIR__ . '../../error/403.php';
}
?>