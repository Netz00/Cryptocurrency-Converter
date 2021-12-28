<?php

/*!
 * This class handles DB connection and its inherited by all classes
 */


abstract class db_connect
{

    protected $db;

    protected function __construct($db = NULL)
    {

        if (is_object($db)) {

            $this->db = $db;

        }  else  {

            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;

            try  {

                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

//                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

            } catch (Exception $e) {

                die ($e->getMessage());
            }
        }
    }
}
