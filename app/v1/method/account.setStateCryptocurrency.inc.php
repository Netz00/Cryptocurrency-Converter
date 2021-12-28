<?php

if (!empty($_POST)) {

    if (!admin::isSession())
        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");



    $id = isset($_POST['id']) ? $_POST['id'] : -1;
    $state = isset($_POST['state']) ? $_POST['state'] : -1;

    $id = helper::clearInt($id);
    $state = helper::clearInt($state);


    if ($id == -1 || $state == -1)
        api::printError(ERROR_ACCESS_TOKEN, "Wrong parameters sent.");



    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $cryptocoin = new cryptocoin($dbo);

    $result =  $cryptocoin->patch($id, $state);

    echo json_encode($result);
    exit;
}
