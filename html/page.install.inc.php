<?php

/**
 * Dont touch this
 * This is only for developers
 */
include_once("../sys/core/initialize.inc.php");

echo "DB installed, check&refresh your DB panel! 💾";

echo "<br><br>";

echo apcu_enabled() ? "apcu cache enabled." : "Please enable apc cache.";

//clear cache
if (apcu_enabled())
    apcu_clear_cache();

echo "<br><br>";

//Add admin user and install database

if (admin::isSession()) {

    echo "You are already signed in as admin...";

    echo "<br><br>";


    //header("Location: /",  true,  301);
    exit;
}

$admin = new admin($dbo);

if ($admin->getCount() > 0) {

    echo "Admin account already exists and database is installed...";

    echo "<br><br>";

    //header("Location: /",  true,  301);
    exit;
}


//include_once("../sys/core/initialize.inc.php");

$page_id = "install";


$error = false;
$error_message = array();


$user_username = 'Netz00';
$user_fullname = 'Admin Netz00';
$user_password = 'a9Rhdkdwd1da';

$error_username = false;
$error_fullname = false;
$error_password = false;


$error = false;


$user_username = helper::clearText($user_username);
$user_fullname = helper::clearText($user_fullname);
$user_password = helper::clearText($user_password);

$user_username = helper::escapeText($user_username);
$user_fullname = helper::escapeText($user_fullname);
$user_password = helper::escapeText($user_password);


if (!helper::isCorrectLogin($user_username)) {

    $error = true;
    $error_username = true;
    $error_message[] = 'Incorrect username.';
}

if (!helper::isCorrectPassword($user_password)) {

    $error = true;
    $error_password = true;
    $error_message[] = 'Incorrect password.';
}

if (!$error) {


    echo "Admin credentils valid...";

    echo "<br><br>";

    $admin = new admin($dbo);

    // Create admin account

    $result = array();
    $result = $admin->signup($user_username, $user_password, $user_fullname);

    if ($result['error'] === false) {

        echo "Admin signed up...";

        echo "<br><br>";

        $access_data = $admin->signin($user_username, $user_password);

        if ($access_data['error'] === false) {


            echo "You are signed in as admin...";

            echo "<br><br>";

            $clientId = 0; // Desktop version

            admin::createAccessToken();

            admin::setSession($access_data['accountId'], admin::getAccessToken());

            // Redirect to Admin Panel main page



            //header("Location: /admin/main",  true,  301);
            exit;
        }

        echo "Unable to sign you up...";

        echo "<br><br>";


        //header("Location: /install",  true,  301);
        exit;
    }
}


$page_title = APP_TITLE;



var_dump($result);

echo "<br><br>";

var_dump($error_message);
