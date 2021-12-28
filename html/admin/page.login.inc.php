<?php

if (admin::isSession()) {

  header("Location: /admin/main",  true,  301);
  exit;
}

$admin = new admin($dbo);

$user_username = '';

$error = false;
$error_message = '';

if (!empty($_POST)) {

  $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
  $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
  $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

  $user_username = helper::clearText($user_username);
  $user_password = helper::clearText($user_password);

  $user_username = helper::escapeText($user_username);
  $user_password = helper::escapeText($user_password);

  if (helper::getAuthenticityToken() !== $token) {

    $error = true;
    $error_message = 'Error!';
  }

  if (!$error) {

    $access_data = array();

    $admin = new admin($dbo);
    $access_data = $admin->signin($user_username, $user_password);

    if ($access_data['error'] === false) {

      $clientId = 0; // Desktop version

      admin::createAccessToken();

      admin::setSession($access_data['accountId'], admin::getAccessToken());

      header("Location: /admin/main",  true,  301);
      exit;
    } else {

      $error = true;
      $error_message = 'Incorrect login or password.';
    }
  }
}

helper::newAuthenticityToken();


$page_title = "Admin| Log In";

?>

<!DOCTYPE html>
<html lang="<?php echo $LANG['lang-code']; ?>">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Cryptocurrency - Admin Panel</title>
  <!-- Favicon-->
  <link rel="icon" type="image/x-icon" href="photo/bitcoin.png" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link href="/css/adminLogin.css" rel="stylesheet" type="text/css" />

</head>

<body>

  <div class="login-card">
    <div class="login-card-content">
      <form id="loginform" action="/admin/login" method="post">
        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
        <p class="form-error-message" style="<?php if (!$error) echo "display: none"; ?>"><?php echo $error_message; ?></p>

        <div class="header">
          <div class="logo">
            <img src="/photo/logo2.png" width="60" height="60" class="d-inline-block align-top" alt="">
          </div>
        </div>
        <div class="form">
          <div class="form-field username">
            <div class="icon">
              <i class="far fa-user"></i>
            </div>
            <input class="form-control form-control-line" type="text" required="" placeholder="" name="user_username" value="<?php echo $user_username; ?>">
          </div>
          <div class="form-field password">
            <div class="icon">
              <i class="fas fa-lock"></i>
            </div>
            <input class="form-control" type="password" required="" placeholder="" name="user_password" value="">
          </div>

          <button type="submit">
            Login
          </button>

        </div>
      </form>
    </div>

  </div>

</body>

</html>