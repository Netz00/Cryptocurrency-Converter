<?php

$C = array();
$B = array();



// Data for the title of the website and copyright

$B['APP_NAME'] = "Cryptocurrency Converter";                   //
$B['APP_TITLE'] = "Cryptocurrency Converter";                  //
$B['APP_YEAR'] = "2021";                                // Year in footer

// Your domain (host) and url! See comments! Carefully!

$B['APP_HOST'] = "127.0.0.1";                 //edit to your domain, example (WARNING - without http:// and www): yourdomain.com
$B['APP_URL'] = "http://localhost";           //edit to your domain url, example (WARNING - with http://): http://yourdomain.com


$B['FIATS'] = ['USD'];
//$B['CRYPTOS'] = ['BTCUSDT', 'ETHUSDT',  'TWTUSDT', 'LTCUSDT', 'BNBUSDT']; admin support for changing cyptocoins added

$B['APCU_TTL'] = 120;


$C['DB_HOST'] = "mysql-db-crypto-converter";                                     //localhost or your db host
$C['DB_USER'] = "root";                                         //your db user
$C['DB_PASS'] = "";                                             //your db password
$C['DB_NAME'] = "crypto_converter";                             //your db name


$C['ERROR_SUCCESS'] = 0;

$C['ERROR_UNKNOWN'] = 100;
$C['ERROR_ACCESS_TOKEN'] = 101;
$C['COULDNT_GET_KEY_FROM_CACHE'] = 102; // vjerovatno je spremljen krivi keyword u cache il nije spremljen uopce
$C['API_CONNECTION_FAILED'] = 104;      // probably api endpoint is unavailable

$C['ERROR_LOGIN_TAKEN'] = 300;
$C['ERROR_EMAIL_TAKEN'] = 301;

$C['ERROR_ACCOUNT_ID'] = 400;

$C['USER_CREATED_SUCCESSFULLY'] = 0;
$C['USER_CREATE_FAILED'] = 1;
$C['USER_ALREADY_EXISTED'] = 2;
$C['USER_BLOCKED'] = 3;
$C['USER_NOT_FOUND'] = 4;
$C['USER_LOGIN_SUCCESSFULLY'] = 5;
$C['EMPTY_DATA'] = 6;
$C['ERROR_API_KEY'] = 7;



$LANGS = array();
$LANGS['English'] = "en";
$LANGS['Русский'] = "ru";
