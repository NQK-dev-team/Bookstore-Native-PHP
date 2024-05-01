<!DOCTYPE html>
<html lang="en">

<head>
      <?php
      require_once __DIR__ . "/../../head_element/cdn.php";
      require_once __DIR__ . "/../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="NQK Bookstore terms of service">
      <title>Term of Service</title>
      <?php
      require_once __DIR__ . '/../../head_element/google_analytic.php';
      ?>
</head>

<body>
      <?php
      require_once __DIR__ . '/../../tool/php/session_check.php';

      if (check_session()) {
            if ($_SESSION['type'] === 'admin')
                  require_once __DIR__ . '/../../layout/admin/header.php';
            else if ($_SESSION['type'] === 'customer')
                  require_once __DIR__ . '/../../layout/customer/header.php';
      } else {
            if (str_contains($_SERVER['REQUEST_URI'], '/admin'))
                  require_once __DIR__ . '/../../layout/admin/header.php';
            else
                  require_once __DIR__ . '/../../layout/customer/header.php';
      }
      ?>
      <section id="page">
            <div class='container-fluid d-flex flex-column'>
                  <h1 class='mt-2 mx-auto text-center'>TERMS OF SERVICE</h1>
                  <hr>
                  <p>Welcome to <strong>NQK Bookstore!</strong> These Terms of Service outline the terms and conditions governing your use of our website, services, and products. By accessing or using our website, you agree to comply with these terms. Please read them carefully before proceeding.</p>
                  <h4>1. Acceptance of Terms</h4>
                  <p><strong>1.1.</strong> By accessing or using <strong>NQK Bookstore</strong>, you agree to be bound by these <strong>Terms of Service</strong> and all applicable laws and regulations.</p>
                  <p><strong>1.2.</strong> If you do not agree with any part of these terms, you may not use our services or access our website.</p>
                  <h4>2. Use of the Website</h4>
                  <p><strong>2.1.</strong> You must be at least 18 years old or have the legal capacity to enter into contracts to use our services.</p>
                  <p><strong>2.2.</strong> You agree to provide accurate and current information when creating an account or making a purchase.</p>
                  <p><strong>2.3.</strong> You are responsible for maintaining the confidentiality of your account information and password.</p>
                  <p><strong>2.4.</strong> You must not use our website for any illegal or unauthorized purpose.</p>
                  <h4>3. Intellectual Property</h4>
                  <p><strong>3.1.</strong> All content, trademarks, logos, and materials on <strong>NQK Bookstore</strong> are the property of <strong>NQK Bookstore</strong> or its licensors.</p>
                  <p><strong>3.2.</strong> You may not use, reproduce, modify, distribute, or display any content from our website without prior written consent.</p>
                  <h4>4. Product Information</h4>
                  <p><strong>4.1.</strong> We strive to provide accurate product descriptions, prices, and availability information. However, we do not guarantee that all information is current, complete, or error-free.</p>
                  <p><strong>4.2.</strong> We reserve the right to modify or discontinue products or services at any time without notice.</p>
                  <h4>5. Orders and Payments</h4>
                  <p><strong>5.1.</strong> By placing an order on NQK Bookstore, you agree to pay the specified price for the products and any applicable taxes and shipping fees.</p>
                  <p><strong>5.2.</strong> Payments are processed securely through our designated payment gateway.</p>
                  <p><strong>5.3.</strong> We reserve the right to refuse or cancel any orders, limit quantities, or terminate accounts at our discretion.</p>
                  <h4>6. Shipping and Delivery</h4>
                  <p><strong>6.1.</strong> We aim to process and ship orders promptly, but delivery times may vary depending on your location and other factors.</p>
                  <p><strong>6.2.</strong> Shipping costs and delivery estimates are provided during checkout and may vary based on the shipping method selected.</p>
                  <h4>7. Returns and Refunds</h4>
                  <p><strong>7.1.</strong> We accept returns and provide refunds or exchanges for eligible products within a specified period from the date of purchase.</p>
                  <p><strong>7.2.</strong> Returned items must be in their original condition and packaging, and you are responsible for return shipping costs unless the return is due to our error.</p>
                  <h4>8. Privacy Policy</h4>
                  <p><strong>8.1.</strong> Your privacy is important to us. Please review our <a href='/privacy-policy' class='text-decoration-none'>Privacy Policy</a> to understand how we collect, use, and protect your personal information.</p>
                  <p><strong>8.2.</strong> By using <strong>NQK Bookstore</strong>, you consent to the collection and use of your information as described in our <a href='/privacy-policy' class='text-decoration-none'>Privacy Policy</a>.</p>
                  <h4>9. Limitation of Liability</h4>
                  <p><strong>9.1.</strong> <strong>NQK Bookstore</strong> and its affiliates shall not be liable for any direct, indirect, incidental, special, or consequential damages arising from your use of our website or products.</p>
                  <p><strong>9.2.</strong> In no event shall our total liability exceed the amount you paid for the specific product or service giving rise to the claim.</p>
                  <h4>10. Indemnification</h4>
                  <p><strong>10.1.</strong> You agree to indemnify and hold <strong>NQK Bookstore</strong>, its officers, directors, employees, and agents harmless from any claims, damages, or losses arising from your use of our services or violation of these <strong>Terms of Service</strong>.</p>
                  <h4>11. Governing Law</h4>
                  <p><strong>11.1.</strong> These <strong>Terms of Service</strong> shall be governed by and construed in accordance with the laws of <strong>United States of America</strong>, without regard to its conflict of law provisions.</p>
                  <p><strong>11.2.</strong> Any disputes arising from these terms shall be resolved through arbitration in <strong>United States of America</strong> according to the rules of the <strong>American Arbitration Association</strong>.</p>
                  <h4>12. Changes to Terms</h4>
                  <p><strong>12.1.</strong> <strong>NQK Bookstore</strong> reserves the right to update or modify these <strong>Terms of Service</strong> at any time without prior notice.</p>
                  <p><strong>12.2.</strong> Continued use of our website after changes are posted constitutes your acceptance of the revised terms.</p>
                  <p>If you have any questions or concerns about these Terms of Service, please contact us at <strong>nqk.demo@gmail.com</strong>.</p>
                  <p>Thank you for choosing <strong>NQK Bookstore</strong>. Happy reading!</p>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../../layout/footer.php';
      ?>
</body>

</html>