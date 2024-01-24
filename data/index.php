
<?php
require_once __DIR__ . '/../tool/php/session_check.php';
require_once __DIR__ . '/../tool/php/converter.php';
require_once __DIR__ . '/../tool/php/sanitizer.php';

if (check_session() && $_SESSION['type'] === 'admin') {
      $requestedUri = sanitize(rawurldecode($_SERVER['REQUEST_URI']));
      $filePath = dirname(__DIR__) . $requestedUri;

      $contentType = mime_content_type($filePath);
      if ($contentType === false) {
            echo json_encode(['error' => 'File not found or something is wrong!']);
            http_response_code(500);
            exit;
      }

      # If contentType is not pdf or image, then it is not allowed to be accessed
      if ($contentType !== 'application/pdf' && $contentType !== 'image/png' && $contentType !== 'image/jpeg') {
            echo json_encode(['error' => 'File type invalid!']);
            http_response_code(400);
            exit;
      }

      // Set the appropriate Content-Type header
      header('Content-Type: ' . $contentType);
      header('Content-Length: ' . filesize($filePath));

      // Fetch and output the content of the requested URI
      ob_clean();
      readfile($filePath);
      flush();
      exit;
} else {
      $requestedUri = sanitize(rawurldecode($_SERVER['REQUEST_URI']));
      $filePath = dirname(__DIR__) . $requestedUri;
      $contentType = mime_content_type($filePath);

      if ($contentType === false) {
            echo json_encode(['error' => 'File not found or something is wrong!']);
            http_response_code(500);
            exit;
      }

      if ($contentType === 'image/png' || $contentType === 'image/jpeg') {
            // Set the appropriate Content-Type header
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . filesize($filePath));

            // Fetch and output the content of the requested URI
            ob_clean();
            readfile($filePath);
            flush();
            exit;
      } else if ($contentType === 'application/pdf') {
            if (check_session() && $_SESSION['type'] === 'customer') {
            }
            else
            {
                  echo json_encode(['error' => 'Not authenticated!']);
                  http_response_code(401);
                  exit;
            }
      } else {
            echo json_encode(['error' => 'File type invalid!']);
            http_response_code(400);
            exit;
      }
}
?>