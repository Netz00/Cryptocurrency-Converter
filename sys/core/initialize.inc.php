<?php

/*!
 * Netz00 2021
 * CryptoConverter
 */

try {


  $sth = $dbo->prepare("CREATE TABLE IF NOT EXISTS admins (
                            id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                            username VARCHAR(50) NOT NULL DEFAULT '',
                            salt CHAR(3) NOT NULL DEFAULT '',
                            createAt int(11) UNSIGNED DEFAULT 0,
							              password VARCHAR(32) NOT NULL DEFAULT '',
                            email VARCHAR(64) NOT NULL DEFAULT '',
                            fullname VARCHAR(150) NOT NULL DEFAULT '',
                            lasttime INT(10) UNSIGNED DEFAULT 0,
                            last_authorize INT(10) UNSIGNED DEFAULT 0,
                            ip_addr CHAR(32) NOT NULL DEFAULT '',
                            lang CHAR(10) DEFAULT 'en',
                            PRIMARY KEY  (id), UNIQUE KEY (username)
                        ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci");
  $sth->execute();

  $sth = $dbo->prepare("CREATE TABLE IF NOT EXISTS cryptocoins (
                            id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                            state TINYINT UNSIGNED DEFAULT 0,
                            name varchar(32) DEFAULT '',
                            symbol VARCHAR(6) NOT NULL DEFAULT '',
                            codename varchar(32) DEFAULT '',
                            PRIMARY KEY  (id), UNIQUE KEY (name), UNIQUE KEY (symbol), UNIQUE KEY (codename)
                            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci");
  $sth->execute();
} catch (Exception $e) {

  die($e->getMessage());
}
