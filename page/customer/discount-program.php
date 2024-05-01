<?php
require_once __DIR__ . '/../../config/db_connection.php';

// Connect to MySQL
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

// Check connection
if (!$conn) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}

$conversion = '';
$stmt = $conn->prepare("select pointConversionRate from pointConfig where locker='X'");
if (!$stmt) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
if (!$stmt->execute()) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
$conversion = $stmt->get_result()->fetch_assoc()['pointConversionRate'];
$stmt->close();

$loyal = '';
$stmt = $conn->prepare('select name,point,discount from discount join customerDiscount on discount.id = customerDiscount.id order by point');
if (!$stmt) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
if (!$stmt->execute()) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
$result = $stmt->get_result();
$idx = 1;
while ($row = $result->fetch_assoc()) {
      $loyal .= "<tr>
      <td>{$idx}</td>
      <td class='text-nowrap'>{$row['name']}</td>
      <td>{$row['point']}</td>
      <td>{$row['discount']}%</td>
      </tr>";
      $idx++;
}
$stmt->close();

$ref = '';
$stmt = $conn->prepare('select name,numberOfPeople,discount from discount join referrerDiscount on discount.id = referrerDiscount.id order by numberOfPeople');
if (!$stmt) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
if (!$stmt->execute()) {
      http_response_code(500);
      require_once __DIR__ . '/../../error/500.php';
      exit;
}
$result = $stmt->get_result();
$idx = 1;
while ($row = $result->fetch_assoc()) {
      $ref .= "<tr>
      <td>{$idx}</td>
      <td class='text-nowrap'>{$row['name']}</td>
      <td>{$row['numberOfPeople']}</td>
      <td>{$row['discount']}%</td>
      </tr>";
      $idx++;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
      <?php
      require_once __DIR__ . "/../../head_element/cdn.php";
      require_once __DIR__ . "/../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="NQK Bookstore discount program">
      <title>Discount Program</title>
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
                  <h1 class='mt-2 mx-auto text-center'>DISCOUNT PROGRAM</h1>
                  <hr>
                  <h4>1. Loyalty Program</h4>
                  <p><?php echo $conversion; ?>% of the order price will be converted to accumulating points.</p>
                  <div class='overflow-x-auto w-100 border rounded'>
                        <table class="table table-bordered w-100 m-0">
                              <thead>
                                    <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Point</th>
                                          <th scope="col">Discount</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <?php echo $loyal; ?>
                              </tbody>
                        </table>
                  </div>
                  <br><br>
                  <h4>2. Referrer Program</h4>
                  <div class='overflow-x-auto w-100 border rounded'>
                        <table class="table table-bordered w-100 m-0">
                              <thead>
                                    <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Name</th>
                                          <th scope="col">Point</th>
                                          <th scope="col">Discount</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <?php echo $ref; ?>
                              </tbody>
                        </table>
                  </div>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../../layout/footer.php';
      ?>
</body>

</html>