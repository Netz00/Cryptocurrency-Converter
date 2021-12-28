<?php


    if (!admin::isSession()) {

        header('Location: /',  true,  301 );  
        exit;
    }

    if (isset($_GET['access_token'])) {

        $accessToken = (isset($_GET['access_token'])) ? ($_GET['access_token']) : '';
        $continue = (isset($_GET['continue'])) ? ($_GET['continue']) : '/';

        if (admin::getAccessToken() === $accessToken) {

            admin::unsetSession();

            header('Location: '.$continue,  true,  301 );  
            exit;
        }
    }

    header('Location: /',  true,  301 );  
    exit;