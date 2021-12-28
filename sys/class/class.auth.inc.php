<?php

/*!
 * This class handles users authorisation
 * 
 */

class auth extends db_connect
{
    private $auth_valid_sec = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

        $this->auth_valid_sec = 7 * 24 * 3600; // 7 days
    }

    static function clearCookie()
    {
        @setcookie('user_name', null, -1, '/');
        @setcookie("user_password", null, -1, '/');
    }

    static function updateCookie($user_name, $access_token)
    {
        @setcookie('user_name', "{$user_name}", time() + 7 * 24 * 3600, "/");
        @setcookie('user_password', "$access_token", time() + 7 * 24 * 3600, "/");
    }



    protected function getUserId($user_login)
    {
        $stmt = $this->db->prepare("SELECT id FROM tb_accounts WHERE user_login = (:user_login) LIMIT 1");
        $stmt->bindParam(":user_login", $user_login, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            return $row['id'];
        }

        return 0;
    }

    protected function getUserLogin($user_id)
    {
        $stmt = $this->db->prepare("SELECT login FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            return $row['login'];
        }

        return 0;
    }

    protected function getUserEmail($user_id)
    {
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            return $row['email'];
        }

        return 0;
    }

    static function isSession()
    {
        if (isset($_SESSION) && isset($_SESSION['user_id'])) {

            return true;

        } else {

            return false;
        }
    }

    static function setSession($user_id, $user_login, $access_level, $access_token)
    {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_login'] = $user_login;
        $_SESSION['access_level'] = $access_level;
        $_SESSION['create_at'] = time();
        $_SESSION['access_token'] = $access_token;
    }

    static function unsetSession()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_login']);
        unset($_SESSION['access_level']);
        unset($_SESSION['create_at']);
        unset($_SESSION['access_token']);
    }

    public function setActivationSession($access_data)
    {
        $_SESSION['activation_user_id'] = $access_data['user_id'];
        $_SESSION['activation_access_token'] = $access_data['access_token'];
    }

    public function unsetActivationSession()
    {

        unset($_SESSION['activation_user_id']);
        unset($_SESSION['activation_access_token']);
    }

    static function unsetAuthorizationSession()
    {
        unset($_SESSION['signup_with']);
        unset($_SESSION['social_id']);
        unset($_SESSION['social_username']);
        unset($_SESSION['social_email']);
    }

    static function getCurrentUserId()
    {
        if (isset($_SESSION) && isset($_SESSION['user_id'])) {

            return $_SESSION['user_id'];

        } else {

            return 0;
        }
    }

    static function getCurrentUserLogin()
    {
        if (isset($_SESSION) && isset($_SESSION['user_login'])) {

            return $_SESSION['user_login'];

        } else {

            return 'undefined';
        }
    }

    static function getCurrentAccessLevel()
    {
        if (isset($_SESSION) && isset($_SESSION['access_level'])) {

            return $_SESSION['access_level'];

        } else {

            return 0;
        }
    }

    static function getAccessToken()
    {
        if (isset($_SESSION) && isset($_SESSION['access_token'])) {

            return $_SESSION['access_token'];

        } else {

            return "undefined";
        }
    }

    static function newAuthenticityToken()
    {

        $authenticity_token = md5(uniqid(rand(), true));

        if (isset($_SESSION)) {

            $_SESSION['authenticity_token'] = $authenticity_token;
        }
    }

    static function getAuthenticityToken()
    {
        if (isset($_SESSION) && isset($_SESSION['authenticity_token'])) {

            return $_SESSION['authenticity_token'];

        } else {

            return NULL;
        }
    }

    static function isActivationSession()
    {
        if (isset($_SESSION) && isset($_SESSION['activation_access_token'])) {

            return true;

        } else {

            return false;
        }
    }

    static function generateSalt($n = 3)
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        $counter = strlen($pattern)-1;

        for ($i=0; $i<$n; $i++) {

            $key .= $pattern{rand(0,$counter)};
        }

        return $key;
    }
}
