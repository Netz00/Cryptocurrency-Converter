<?php

if (!empty($_POST)) {

    if (!admin::isSession())
        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");


    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $symbol = isset($_POST['symbol']) ? $_POST['symbol'] : '';
    $codename = isset($_POST['codename']) ? $_POST['codename'] : '';

    if ($name == '' || $symbol == '' || $codename == '')
        api::printError(ERROR_ACCESS_TOKEN, "Wrong parameters sent.");

    $name = helper::clearText($name);
    $symbol = helper::escapeText($symbol);
    $codename = helper::escapeText($codename);


    $name = helper::clearText($name);
    $symbol = helper::escapeText($symbol);
    $codename = helper::clearText($codename);


    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $cryptocoin = new cryptocoin($dbo);

    $result =  $cryptocoin->create($name, $symbol, $codename, 0);





    echo json_encode($result);
    exit;
}
