<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (isset($_COOKIE['lang'])) {

        $language = $_COOKIE['lang'];

        $result = "en";

        if (in_array($language, $LANGS)) {

            $result = $language;
        }

        @setcookie("lang", $result, time() + 14 * 24 * 3600, "/");
        include_once("../sys/lang/".$result.".php");

    }  else {

        $language = "en";

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }

        $result = "en";

        if (in_array($language, $LANGS)) {

            $result = $language;
        }

        @setcookie("lang", $result, time() + 14 * 24 * 3600, "/");
        include_once("../sys/lang/".$result.".php");
    }

    $LANG = $TEXT;
