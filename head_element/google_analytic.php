<?php
// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['ENVIRONMENT'] === 'development') {
?>
      <script async src="https://www.googletagmanager.com/gtag/js?id=G-M5V2XFMWW4"></script>
      <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                  dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'G-M5V2XFMWW4');
      </script>
<?php
} else if ($_ENV['ENVIRONMENT'] === 'live') {
?>
      <script async src="https://www.googletagmanager.com/gtag/js?id=G-CCJYFWDBCP"></script>
      <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                  dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'G-CCJYFWDBCP');
      </script>

<?php } ?>