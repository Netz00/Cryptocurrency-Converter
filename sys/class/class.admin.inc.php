<?php

/*!
 * This class handles admin account managing
 */

class admin extends db_connect
{

    private $requestFrom = 0;
    private $id = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }


    public function setAdminStatus($invoiceID, $invoiceAdminStatus)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
        );

        $stmt = $this->db->prepare("UPDATE invoices SET admin_state = (:admin_state), adminId = (:adminId) WHERE id = (:invoiceID)");
        $stmt->bindParam(":adminId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":admin_state", $invoiceAdminStatus, PDO::PARAM_INT);
        $stmt->bindParam(":invoiceID", $invoiceID, PDO::PARAM_INT);
        $stmt->execute();


        if ($stmt->execute()) {
            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;
        }

        return $result;
    }



    //INVOICE FUNCTIONS

    public function get()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT
                invoices.id,invoices.ip_addr,invoices.createAt,invoices.email,invoices.price,
                invoices.fee,invoices.crypto_type,invoices.fiat_type,invoices.random_password,
                invoices.random_selector,invoices.balance,invoices.state,invoices.admin_state,
                admins.fullname,crypto_for_fiat.IBAN,NULL AS wallet_address
            FROM
            invoices
            RIGHT JOIN crypto_for_fiat ON invoices.id = crypto_for_fiat.id
            LEFT JOIN admins ON invoices.adminId = admins.id
            UNION ALL
            SELECT
                invoices.id,invoices.ip_addr,invoices.createAt,invoices.email,invoices.price,
                invoices.fee,invoices.crypto_type,invoices.fiat_type,invoices.random_password,
                invoices.random_selector,invoices.balance,invoices.state,
                invoices.admin_state,admins.fullname,NULL AS IBAN,fiat_for_crypto.wallet_address
            FROM
                invoices
            RIGHT JOIN fiat_for_crypto ON invoices.id = fiat_for_crypto.id
            LEFT JOIN admins ON invoices.adminId = admins.id
            ORDER BY createAt DESC LIMIT 200"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "id" => $row['id'],
                    "ip_addr" => $row['ip_addr'],
                    "date" =>  $row['createAt'],
                    "email" => $row['email'],
                    "price" => $row['price'],
                    "fee" => $row['fee'],
                    "crypto_type" => $row['crypto_type'],
                    "fiat_type" => $row['fiat_type'],
                    "balance" => $row['balance'],
                    "state" => $row['state'],
                    "admin_state" => $row['admin_state'],
                    "fullname" => $row['fullname'],
                    "IBAN" => $row['IBAN'],
                    "random_password" => $row['random_password'],
                    "random_selector" => $row['random_selector'],
                    "wallet_address" => $row['wallet_address']
                );

                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;

        // if (!$stmt->execute()) {
        //     die('Error');
        // }

        // echo "<table>";
        // $tableheader = false;
        // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //     if ($tableheader == false) {
        //         echo '<tr>';
        //         foreach ($row as $key => $value) {
        //             echo "<th>{$key}</th>";
        //         }
        //         echo '</tr>';
        //         $tableheader = true;
        //     }
        //     echo "<tr>";
        //     foreach ($row as $value) {
        //         echo "<td>{$value}</td>";
        //     }
        //     echo "</tr>";
        // }
        // echo "</table>";
    }


    public function getInvoicesByDate($minDate, $maxDate)
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM 
            (SELECT
                invoices.id,invoices.ip_addr,invoices.createAt,invoices.email,invoices.price,
                invoices.fee,invoices.crypto_type,invoices.fiat_type,invoices.random_password,
                invoices.random_selector,invoices.balance,invoices.state,invoices.admin_state,
                admins.fullname,crypto_for_fiat.IBAN,NULL AS wallet_address
            FROM
            invoices
            RIGHT JOIN crypto_for_fiat ON invoices.id = crypto_for_fiat.id
            LEFT JOIN admins ON invoices.adminId = admins.id
            UNION ALL
            SELECT
                invoices.id,invoices.ip_addr,invoices.createAt,invoices.email,invoices.price,
                invoices.fee,invoices.crypto_type,invoices.fiat_type,invoices.random_password,
                invoices.random_selector,invoices.balance,invoices.state,
                invoices.admin_state,admins.fullname,NULL AS IBAN,fiat_for_crypto.wallet_address
            FROM
                invoices
            RIGHT JOIN fiat_for_crypto ON invoices.id = fiat_for_crypto.id
            LEFT JOIN admins ON invoices.adminId = admins.id
            ORDER BY createAt DESC)
            AS U
            WHERE U.createAt BETWEEN (:minDate) AND (:maxDate);"
        );

        $stmt->bindParam(":minDate", $minDate, PDO::PARAM_STR);
        $stmt->bindParam(":maxDate", $maxDate, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "id" => $row['id'],
                    "ip_addr" => $row['ip_addr'],
                    "date" =>  $row['createAt'],
                    "email" => $row['email'],
                    "price" => $row['price'],
                    "fee" => $row['fee'],
                    "crypto_type" => $row['crypto_type'],
                    "fiat_type" => $row['fiat_type'],
                    "balance" => $row['balance'],
                    "state" => $row['state'],
                    "admin_state" => $row['admin_state'],
                    "fullname" => $row['fullname'],
                    "IBAN" => $row['IBAN'],
                    "random_password" => $row['random_password'],
                    "random_selector" => $row['random_selector'],
                    "wallet_address" => $row['wallet_address']
                );

                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;
    }


    public function getCrypto()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM invoices
            RIGHT JOIN fiat_for_crypto ON invoices.id = fiat_for_crypto.id
            ORDER BY createAt DESC LIMIT 200"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "id" => $row['id'],
                    "email" => $row['email'],
                    "date" => date("Y-m-d H:i:s", $row['createAt']),
                    "price" => $row['price'],
                    "fee" => $row['fee'],
                    "crypto_type" => $row['crypto_type'],
                    "fiat_type" => $row['fiat_type'],
                    "balance" => $row['balance'],
                    "wallet_address" => $row['wallet_address'],
                    "ip_addr" => $row['ip_addr'],
                    "state" => $row['state'],
                    "adminId" => $row['adminId'],
                    "admin_state" => $row['admin_state']
                );

                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;
    }




    public function getFiat()
    {

        $result = array(
            "error" => false,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM invoices
            RIGHT JOIN crypto_for_fiat ON invoices.id = crypto_for_fiat.id
            ORDER BY createAt DESC LIMIT 200"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "id" => $row['id'],
                    "email" => $row['email'],
                    "date" => date("Y-m-d H:i:s", $row['createAt']),
                    "price" => $row['price'],
                    "fee" => $row['fee'],
                    "crypto_type" => $row['crypto_type'],
                    "fiat_type" => $row['fiat_type'],
                    "balance" => $row['balance'],
                    "IBAN" => $row['IBAN'],
                    "ip_addr" => $row['ip_addr'],
                    "state" => $row['state'],
                    "adminId" => $row['adminId'],
                    "admin_state" => $row['admin_state']
                );

                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;
    }



    public function getAdmins()
    {

        $result = array(
            "error" => false,
            "error_code" => ERROR_UNKNOWN,
            "items" => array()
        );

        $stmt = $this->db->prepare(
            "SELECT id, fullname FROM admins"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;

            while ($row = $stmt->fetch()) {

                $itemInfo = array(
                    "id" => $row['id'],
                    "fullname" => $row['fullname'],
                );

                array_push($result['items'], $itemInfo);
                unset($itemInfo);
            }
        }

        return $result;
    }

    public function getFiatForCryptoSum()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
        );

        $stmt = $this->db->prepare(
            "SELECT SUM(invoices.balance/invoices.price) AS amount, COUNT(*) AS count 
            FROM invoices
            RIGHT JOIN fiat_for_crypto ON invoices.id = fiat_for_crypto.id;"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;


            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "count" => $row['count'],
                    "amount" => $row['amount'],
                );
            }
        }

        return $result;
    }

    public function getCryptoForFiatSum()
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
        );

        $stmt = $this->db->prepare(
            "SELECT SUM(invoices.balance*invoices.price) AS amount, COUNT(*) AS count 
            FROM invoices
            RIGHT JOIN crypto_for_fiat ON invoices.id = crypto_for_fiat .id;"
        );

        if ($stmt->execute()) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;


            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "count" => $row['count'],
                    "amount" => $row['amount'],
                );
            }
        }

        return $result;
    }

    public function getCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM admins");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }


    public function signin($username, $password)
    {
        $result = array(
            'error' => true,
            "error_code" => ERROR_UNKNOWN
        );

        $username = helper::clearText($username);
        $password = helper::clearText($password);

        $stmt = $this->db->prepare("SELECT salt FROM admins WHERE username = (:username) LIMIT 1");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password) . $row['salt']);

            $stmt2 = $this->db->prepare("SELECT id FROM admins WHERE username = (:username) AND password = (:password) LIMIT 1");
            $stmt2->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $row2 = $stmt2->fetch();

                $result = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "accountId" => $row2['id']
                );
            }
        }

        return $result;
    }



    public function setId($accountId)
    {
        $this->id = $accountId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    static function isSession()
    {
        if (isset($_SESSION) && isset($_SESSION['admin_id'])) {

            return true;
        } else {

            return false;
        }
    }

    static function getCurrentAdminId()
    {
        if (admin::isSession()) {

            return $_SESSION['admin_id'];
        } else {

            return 0;
        }
    }

    static function setSession($admin_id, $access_token)
    {
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_access_token'] = $access_token;
    }

    static function unsetSession()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_access_token']);
    }

    static function getAccessToken()
    {
        if (isset($_SESSION) && isset($_SESSION['admin_access_token'])) {

            return $_SESSION['admin_access_token'];
        } else {

            return "undefined";
        }
    }

    static function createAccessToken()
    {
        $access_token = md5(uniqid(rand(), true));

        if (isset($_SESSION)) {

            $_SESSION['admin_access_token'] = $access_token;
        }
    }




    // Only admin funcitons,
    // user will get access at v2.0

    public function signup($username, $password, $fullname)
    {

        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        if (!helper::isCorrectLogin($username)) {

            $result = array(
                "error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 0,
                "error_description" => "Incorrect login"
            );

            return $result;
        }

        if (!helper::isCorrectPassword($password)) {

            $result = array(
                "error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 1,
                "error_description" => "Incorrect password"
            );

            return $result;
        }

        $salt = helper::generateSalt(3);
        $passw_hash = md5(md5($password) . $salt);
        $currentTime = time();

        $stmt = $this->db->prepare("INSERT INTO admins (username, salt, password, fullname, createAt) value (:username, :salt, :password, :fullname, :createAt)");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
        $stmt->bindParam(":password", $passw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $this->setId($this->db->lastInsertId());

            $result = array(
                "error" => false,
                'accountId' => $this->id,
                'username' => $username,
                'password' => $password,
                'error_code' => ERROR_SUCCESS,
                'error_description' => 'SignUp Success!'
            );

            return $result;
        }

        return $result;
    }

    public function setPassword($password, $newPassword)
    {
        $result = array(
            'error' => true,
            'error_code' => ERROR_UNKNOWN
        );

        if (!helper::isCorrectPassword($password)) {

            return $result;
        }

        if (!helper::isCorrectPassword($newPassword)) {

            return $result;
        }

        $stmt = $this->db->prepare("SELECT salt FROM admins WHERE id = (:adminId) LIMIT 1");
        $stmt->bindParam(":adminId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password) . $row['salt']);

            $stmt2 = $this->db->prepare("SELECT id FROM admins WHERE id = (:adminId) AND password = (:password) LIMIT 1");
            $stmt2->bindParam(":adminId", $this->id, PDO::PARAM_INT);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $this->newPassword($newPassword);

                $result = array(
                    "error" => false,
                    "error_code" => ERROR_SUCCESS
                );
            }
        }

        return $result;
    }

    public function newPassword($password)
    {
        $newSalt = helper::generateSalt(3);
        $newHash = md5(md5($password) . $newSalt);

        $stmt = $this->db->prepare("UPDATE admins SET password = (:newHash), salt = (:newSalt) WHERE id = (:adminId)");
        $stmt->bindParam(":adminId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":newHash", $newHash, PDO::PARAM_STR);
        $stmt->bindParam(":newSalt", $newSalt, PDO::PARAM_STR);
        $stmt->execute();
    }
}
