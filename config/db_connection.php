
<?php

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db_host = $_ENV['DB_HOST'] ? $_ENV['DB_HOST'] : "localhost";
$db_database = $_ENV['DB_SCHEMA'] ? $_ENV['DB_SCHEMA'] : "bookstore";
$db_user = $_ENV['DB_USER'] ? $_ENV['DB_USER'] : "bookstore";
$db_password = $_ENV['DB_PASSWORD'] ? $_ENV['DB_PASSWORD'] : "bookstore123";
$db_port = $_ENV['DB_PORT'] ? $_ENV['DB_PORT'] : "3306";

?>