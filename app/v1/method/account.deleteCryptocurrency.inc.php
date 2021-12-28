<?php

if (!empty($_POST)) {

    if (!admin::isSession())
        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");





    $rowsIDs = json_decode(stripslashes($_POST['rowsIDs']));




    if ($rowsIDs == null)
        api::printError(ERROR_ACCESS_TOKEN, "Wrong parameters sent.");




    $rowsIDs_sanitized = array_map(
        function ($in) {
            return helper::clearInt($in);
        },
        $rowsIDs
    );


    $rows = "";
    foreach ($rowsIDs_sanitized as $row) {
        $rows .= $row . ', ';
    }

    $rows = rtrim($rows, ", ");



    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $cryptocoin = new cryptocoin($dbo);

    $result =  $cryptocoin->delete($rowsIDs_sanitized);

    echo json_encode($result);
    exit;
}
