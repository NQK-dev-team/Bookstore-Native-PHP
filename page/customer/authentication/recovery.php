<?php
require_once __DIR__ . '../../../../tool/php/session_check.php';

if (check_session()) header('Location: /');
unset($_SESSION['update_book_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
      <?php
      require_once __DIR__ . "/../../../head_element/cdn.php";
      require_once __DIR__ . "/../../../head_element/meta.php";
      ?>
      <link rel="stylesheet" href="/css/preset_style.css">

      <meta name="author" content="Nghia Duong">
      <meta name="description" content="Recover lost customer account of NQK Bookstore">
      <title>Recovery</title>
      <link rel="stylesheet" href="/css/authentication/style.css">
      <?php
      require_once __DIR__ . '/../../../head_element/google_analytic.php';
      ?>
</head>

<body>
      <?php
      require_once __DIR__ . '/../../../layout/customer/header.php';
      ?>
      <section id="page">
            <div class='w-100 h-100 position-relative'>
                  <div class='background'></div>
                  <div class="container-fluid h-100 d-flex justify-content-center py-4">
                        <form onsubmit="enterEmail(event,'customer')" class="bg-white border border-black rounded form my-auto flex-column px-3" id="recovery_email_form">
                              <div class='w-100 d-flex flex-column'>
                                    <h1 class="mx-auto mb-0 mt-1">Confirm email</h1>
                                    <div class="align-items-center justify-content-center error_message mt-2 mx-auto" id="recovery_fail_1">
                                          <svg class="ms-1" fill="#ff0000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000" stroke-width="30.72">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M520.741 163.801a10.234 10.234 0 00-3.406-3.406c-4.827-2.946-11.129-1.421-14.075 3.406L80.258 856.874a10.236 10.236 0 00-1.499 5.335c0 5.655 4.585 10.24 10.24 10.24h846.004c1.882 0 3.728-.519 5.335-1.499 4.827-2.946 6.352-9.248 3.406-14.075L520.742 163.802zm43.703-26.674L987.446 830.2c17.678 28.964 8.528 66.774-20.436 84.452a61.445 61.445 0 01-32.008 8.996H88.998c-33.932 0-61.44-27.508-61.44-61.44a61.445 61.445 0 018.996-32.008l423.002-693.073c17.678-28.964 55.488-38.113 84.452-20.436a61.438 61.438 0 0120.436 20.436zM512 778.24c22.622 0 40.96-18.338 40.96-40.96s-18.338-40.96-40.96-40.96-40.96 18.338-40.96 40.96 18.338 40.96 40.96 40.96zm0-440.32c-22.622 0-40.96 18.338-40.96 40.96v225.28c0 22.622 18.338 40.96 40.96 40.96s40.96-18.338 40.96-40.96V378.88c0-22.622-18.338-40.96-40.96-40.96z"></path>
                                                </g>
                                          </svg>
                                          <p class="mb-0 text-danger fw-medium ms-2 me-1" id="error_message_content_1"></p>
                                    </div>
                                    <hr>
                              </div>
                              <div class="form-group">
                                    <label for="inputEmail" class="fs-4 fw-medium">Email</label>
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Enter email" name="email" autocomplete="email">
                              </div>
                              <a class="mx-auto mt-2 text-primary text-decoration-none mb-2" href="/authentication/">Back to login</a>
                              <div class="mt-auto my-3 mx-auto">
                                    <button type="submit" class="btn btn-primary" onclick="clearAllCustomValidity()">Continue</button>
                              </div>
                        </form>

                        <form onsubmit="enterCode(event)" class="bg-white border border-black rounded form my-auto flex-column px-3" id="recovery_code_form">
                              <div class='w-100 d-flex flex-column'>
                                    <h1 class="mx-auto mb-0 mt-1">Recovery code</h1>
                                    <div class="align-items-center justify-content-center error_message mt-2 mx-auto" id="recovery_fail_2">
                                          <svg class="ms-1" fill="#ff0000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000" stroke-width="30.72">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M520.741 163.801a10.234 10.234 0 00-3.406-3.406c-4.827-2.946-11.129-1.421-14.075 3.406L80.258 856.874a10.236 10.236 0 00-1.499 5.335c0 5.655 4.585 10.24 10.24 10.24h846.004c1.882 0 3.728-.519 5.335-1.499 4.827-2.946 6.352-9.248 3.406-14.075L520.742 163.802zm43.703-26.674L987.446 830.2c17.678 28.964 8.528 66.774-20.436 84.452a61.445 61.445 0 01-32.008 8.996H88.998c-33.932 0-61.44-27.508-61.44-61.44a61.445 61.445 0 018.996-32.008l423.002-693.073c17.678-28.964 55.488-38.113 84.452-20.436a61.438 61.438 0 0120.436 20.436zM512 778.24c22.622 0 40.96-18.338 40.96-40.96s-18.338-40.96-40.96-40.96-40.96 18.338-40.96 40.96 18.338 40.96 40.96 40.96zm0-440.32c-22.622 0-40.96 18.338-40.96 40.96v225.28c0 22.622 18.338 40.96 40.96 40.96s40.96-18.338 40.96-40.96V378.88c0-22.622-18.338-40.96-40.96-40.96z"></path>
                                                </g>
                                          </svg>
                                          <p class="mb-0 text-danger fw-medium ms-2 me-1" id="error_message_content_2"></p>
                                    </div>
                                    <hr>
                              </div>
                              <strong class="mb-3" id="user_recovery_email">A recovery code has been sent to your email</strong>
                              <div class="form-group">
                                    <input autocomplete="one-time-code" type="text" class="form-control" id="inputRecoveryCode" placeholder="Enter recovery code" name="recoveryCode">
                              </div>
                              <div class="d-flex align-items-center mt-3">
                                    <p>Didn't receive the code? <span class="text-primary pointer" onclick="requestRecoveryCode()">Request another</span></p>
                              </div>
                              <button type="button" class="btn btn-outline-secondary btn-sm mx-auto" onclick="changeEmail()">Change email</button>
                              <a class="mx-auto mt-2 text-primary text-decoration-none mb-2" href="/authentication/">Back to login</a>
                              <div class="mt-auto my-3 mx-auto">
                                    <button type="submit" class="btn btn-primary" onclick="clearAllCustomValidity()">Continue</button>
                              </div>
                        </form>

                        <form onsubmit="changePassword(event,'customer')" class="bg-white border border-black rounded form my-auto flex-column px-3" id="recovery_password_form">
                              <div class='w-100 d-flex flex-column'>
                                    <h1 class="mx-auto mb-0 mt-1">Change password</h1>
                                    <div class="align-items-center justify-content-center error_message mt-2 mx-auto" id="recovery_fail_3">
                                          <svg class="ms-1" fill="#ff0000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000" stroke-width="30.72">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M520.741 163.801a10.234 10.234 0 00-3.406-3.406c-4.827-2.946-11.129-1.421-14.075 3.406L80.258 856.874a10.236 10.236 0 00-1.499 5.335c0 5.655 4.585 10.24 10.24 10.24h846.004c1.882 0 3.728-.519 5.335-1.499 4.827-2.946 6.352-9.248 3.406-14.075L520.742 163.802zm43.703-26.674L987.446 830.2c17.678 28.964 8.528 66.774-20.436 84.452a61.445 61.445 0 01-32.008 8.996H88.998c-33.932 0-61.44-27.508-61.44-61.44a61.445 61.445 0 018.996-32.008l423.002-693.073c17.678-28.964 55.488-38.113 84.452-20.436a61.438 61.438 0 0120.436 20.436zM512 778.24c22.622 0 40.96-18.338 40.96-40.96s-18.338-40.96-40.96-40.96-40.96 18.338-40.96 40.96 18.338 40.96 40.96 40.96zm0-440.32c-22.622 0-40.96 18.338-40.96 40.96v225.28c0 22.622 18.338 40.96 40.96 40.96s40.96-18.338 40.96-40.96V378.88c0-22.622-18.338-40.96-40.96-40.96z"></path>
                                                </g>
                                          </svg>
                                          <p class="mb-0 text-danger fw-medium ms-2 me-1" id="error_message_content_3"></p>
                                    </div>
                                    <hr>
                              </div>
                              <div class="form-group">
                                    <label for="inputNewPassword" class="fs-4 fw-medium">New password</label>
                                    <input autocomplete="new-password" type="password" class="form-control" id="inputNewPassword" placeholder="Enter new password" name="newPassword" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="New password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters">
                              </div>
                              <div class="form-group mt-3">
                                    <label for="inputConfirmNewPassword" class="fs-4 fw-medium">Confirm new password</label>
                                    <input autocomplete="new-password" type="password" class="form-control" id="inputConfirmNewPassword" placeholder="Confirm new password" name="confirmNewPassword">
                              </div>
                              <button type="button" class="btn btn-outline-secondary btn-sm mx-auto mt-3" onclick="backToGetCode()">Back to recovery code</button>
                              <a class="mx-auto mt-2 text-primary text-decoration-none mb-2" href="/authentication/">Back to login</a>
                              <div class="mt-auto my-3 mx-auto">
                                    <button type="submit" class="btn btn-primary" onclick="clearAllCustomValidity()">Finish</button>
                              </div>
                        </form>
                  </div>
            </div>

            <div class="modal fade" id="passwordChangeModal" tabindex="-1" aria-labelledby="Password changed label">
                  <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                              <div class="modal-header">
                                    <h2 class="modal-title fs-5" id="modalLabel">Password Changed!</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body d-flex flex-column">
                                    <p>Your password has been changed successfully!</p>
                              </div>
                              <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button>
                              </div>
                        </div>
                  </div>
            </div>
      </section>
      <?php
      require_once __DIR__ . '/../../../layout/footer.php';
      ?>
      <script src="/javascript/customer/menu_after_load.js"></script>
      <script src="/tool/js/encoder.js"></script>
      <script src="/tool/js/input_validity.js"></script>
      <script src="/javascript/authentication/recovery.js"></script>
      <script src="/tool/js/tool_tip.js"></script>
</body>

</html>