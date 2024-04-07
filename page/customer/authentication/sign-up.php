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
      <meta name="description" content="Sign up as a new customer to use NQK bookstore's services">
      <title>Sign up</title>
      <link rel="stylesheet" href="/css/authentication/style.css">
</head>

<body>
      <?php
      require_once __DIR__ . '/../../../layout/customer/header.php';
      ?>
      <section id="page">
            <div class='w-100 h-100 position-relative'>
                  <div class='background'></div>
                  <div class="container-fluid h-100 d-flex justify-content-center py-4">
                        <form onsubmit="signUpHandler(event)" class="bg-white border border-black rounded form my-auto d-flex flex-column px-3">
                              <div class='w-100 d-flex flex-column'>
                                    <h1 class="mx-auto mb-0 mt-1">Sign up</h1>
                                    <div class="align-items-center justify-content-center error_message mt-2 mx-auto" id="signup_fail">
                                          <svg class="ms-1" fill="#ff0000" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000" stroke-width="30.72">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M520.741 163.801a10.234 10.234 0 00-3.406-3.406c-4.827-2.946-11.129-1.421-14.075 3.406L80.258 856.874a10.236 10.236 0 00-1.499 5.335c0 5.655 4.585 10.24 10.24 10.24h846.004c1.882 0 3.728-.519 5.335-1.499 4.827-2.946 6.352-9.248 3.406-14.075L520.742 163.802zm43.703-26.674L987.446 830.2c17.678 28.964 8.528 66.774-20.436 84.452a61.445 61.445 0 01-32.008 8.996H88.998c-33.932 0-61.44-27.508-61.44-61.44a61.445 61.445 0 018.996-32.008l423.002-693.073c17.678-28.964 55.488-38.113 84.452-20.436a61.438 61.438 0 0120.436 20.436zM512 778.24c22.622 0 40.96-18.338 40.96-40.96s-18.338-40.96-40.96-40.96-40.96 18.338-40.96 40.96 18.338 40.96 40.96 40.96zm0-440.32c-22.622 0-40.96 18.338-40.96 40.96v225.28c0 22.622 18.338 40.96 40.96 40.96s40.96-18.338 40.96-40.96V378.88c0-22.622-18.338-40.96-40.96-40.96z"></path>
                                                </g>
                                          </svg>
                                          <p class="mb-0 text-danger fw-medium ms-2 me-1" id="error_message_content"></p>
                                    </div>
                                    <hr>
                              </div>
                              <div class="form-group">
                                    <div class="d-flex">
                                          <label for="inputName" class="fs-4 fw-medium">Name</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input autocomplete="name" type="text" class="form-control" id="inputName" placeholder="Enter name" name="name" title="Test">
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="inputDate" class="fs-4 fw-medium">Date of birth</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input autocomplete="bday" type="date" class="form-control" id="inputDate" name="date">
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="inputGender" class="fs-4 fw-medium">Gender</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <select class="form-select" aria-label="Select gender" id='inputGender'>
                                          <option selected value=null>Choose your gender</option>
                                          <option value="M">Male</option>
                                          <option value="F">Female</option>
                                          <option value="O">Other</option>
                                    </select>
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="inputPhone" class="fs-4 fw-medium">Phone number</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input onchange="checkPhoneUsed()" autocomplete="tel" type="tel" class="form-control" id="inputPhone" placeholder="Enter phone number" name="phone">
                                    <div class="mt-2 align-items-center used_error" id="phone_used_error">
                                          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M12 16H12.01M12 8V12M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg>
                                          <p class="ms-1 text-danger mb-0 fw-medium">This phone number has been used!</p>
                                    </div>
                              </div>
                              <div class="form-group mt-3">
                                    <label for="inputAddress" class="fs-4 fw-medium">Address</label>
                                    <p class="mb-1">(For default delivery address usage)</p>

                                    <input autocomplete="off" type="text" class="form-control" id="inputAddress" placeholder="Enter address" name="address">
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="inputEmail" class="fs-4 fw-medium">Email</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input onchange="checkEmailUsed(false)" autocomplete="email" type="email" class="form-control" id="inputEmail" placeholder="Enter email" name="email">
                                    <div class="mt-2 align-items-center used_error" id="email_used_error">
                                          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M12 16H12.01M12 8V12M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg>
                                          <p class="ms-1 text-danger mb-0 fw-medium">This email has been used!</p>
                                    </div>
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="inputPassword" class="fs-4 fw-medium">Password</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input autocomplete="new-password" type="password" class="form-control" id="inputPassword" placeholder="Enter password" name="password" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Password must contain at least one uppercase letter, one lowercase letter, one number, one special character and is within 8 to 72 characters">
                              </div>
                              <div class="form-group mt-3">
                                    <div class="d-flex">
                                          <label for="confirmPassword" class="fs-4 fw-medium">Confirm Password</label>
                                          <p class="text-danger mb-0 ms-2 align-middle text-center fs-4 fw-bold">*</p>
                                    </div>
                                    <input autocomplete="new-password" type="password" class="form-control" id="confirmPassword" placeholder="Enter password" name="confirmPassword">
                              </div>
                              <div class="form-group mt-3">
                                    <label for="inputRefEmail" class="fs-4 fw-medium">Refferer email</label>
                                    <input onchange="checkEmailUsed(true)" autocomplete="email" type="email" class="form-control" id="inputRefEmail" placeholder="Enter email" name="refEmail">
                                    <div class="mt-2 align-items-center used_error" id="ref_email_error">
                                          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                      <path d="M12 16H12.01M12 8V12M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </g>
                                          </svg>
                                          <p class="ms-1 text-danger mb-0 fw-medium">Referrer email not found or invalid!</p>
                                    </div>
                              </div>
                              <a class="mx-auto mt-2 text-primary text-decoration-none mb-2" href="/authentication/">Back to login</a>
                              <div class="mt-auto my-3 mx-auto">
                                    <button type="submit" class="btn btn-primary" onclick="clearAllCustomValidity()">Sign Up</button>
                              </div>
                        </form>
                  </div>
            </div>

            <div class="modal fade" id="signUpSuccessModal" tabindex="-1" aria-labelledby="Sign up success modal">
                  <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                              <div class="modal-header">
                                    <h2 class="modal-title fs-5" id="modalLabel">Sign Up Success</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body d-flex flex-column">
                                    <p>You now can use all the services that NQK Bookstore has to offer</p>
                                    <p class="mx-auto mb-0">Happy shopping!</p>
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
      <script src="/tool/js/dob_checker.js"></script>
      <script src="/javascript/authentication/signup.js"></script>
      <script src="/tool/js/tool_tip.js"></script>
</body>

</html>