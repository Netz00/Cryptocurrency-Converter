<?php

/**
 * Router file
 */

error_reporting(E_ALL);

session_start();

include_once("../sys/core/init.inc.php");

$page_id = '';



if (!empty($_GET)) {

    if (!isset($_GET['q'])) {

        include_once("../html/main.inc.php");
        exit;
    }

    $request = htmlentities($_GET['q'], ENT_QUOTES);
    $request = helper::escapeText($request);
    $request = explode('/', trim($request, '/'));

    $cnt = count($request);

    switch ($cnt) { //switch depth of request

        case 0: {

                include_once("../html/main.inc.php");
                exit;
            }

        case 1: {

                if (file_exists("../html/page." . $request[0] . ".inc.php")) {

                    include_once("../html/page." . $request[0] . ".inc.php");
                    exit;
                } else {

                    include_once("../html/error.inc.php");
                    exit;
                }
            }

            //admin pages
        case 2: {

                if (file_exists("../html/" . $request[0] . "/page." . $request[1] . ".inc.php")) {

                    include_once("../html/" . $request[0] . "/page." . $request[1] . ".inc.php");
                    exit;
                } else {

                    include_once("../html/error.inc.php");
                    exit;
                }
            }


            // api calls
        case 4: {

                switch ($request[0]) {

                    case 'api': {

                            if (file_exists("../app/" . $request[1] . "/" . $request[2] . "/" . $request[3] . ".inc.php")) {

                                include_once("../sys/config/api.inc.php");

                                include_once("../app/" . $request[1] . "/" . $request[2] . "/" . $request[3] . ".inc.php");
                                exit;
                            } else {
                                include_once("../html/error.inc.php");
                                exit;
                            }

                            break;
                        }

                    default: {
                            include_once("../html/error.inc.php");
                            exit;
                        }
                }
            }



        default: {

                include_once("../html/error.inc.php");
                exit;
            }
    }
} else {

    $request = array();
    include_once("../html/main.inc.php");
    exit;
}
