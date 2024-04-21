
<?php
// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$CLIENT_ID = $_ENV['CLIENT_ID'] ? $_ENV['CLIENT_ID'] : "";
$CLIENT_SECRET = $_ENV['CLIENT_SECRET'] ? $_ENV['CLIENT_SECRET'] : "";
?>