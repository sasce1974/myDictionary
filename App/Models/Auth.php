<?php


namespace App\Models;


use App\Config;
use Core\DB_connection;
use Core\Model;
use PDO;

class Auth extends Model
{
    public static $isLogged = false;
    public $errorType = "fatal";

    private static $id;
    //public $ip;

    public static function check(){
        return isset(self::$id);
    }

    public static function id(){
        return self::$id;
    }

    public static function user(){
        self::_initUser();
        $user = new User();
        return $user->find(self::$id);
    }


    public function init($user){
        if (session_id() == "") {
            if(!headers_sent()) {
                session_start();
            }
        }
        if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true) {
            $this->_initUser();
        }
    }


    /**
     * Authenticate the user
     *
     * On successful authentication initiates the Auth user in the session
     *
     * @param $email
     * @param $password
     * @return bool
     */
    public static function authenticate($email, $password){
        $q = "SELECT * from users WHERE email = ?";
        $query = DB_connection::getCon()->prepare($q);
        $query->execute(array($email));
        if($query->rowCount() !== 1){
            $_SESSION['error'][] = "User not found!";
            return false;
        }
        $query->setFetchMode(PDO::FETCH_OBJ);
        $user = $query->fetch();
        $dbPassword = $user->password;
        if (!password_verify($password,$dbPassword)) {
            error_log("Passwords for {$email} don't match");
            $_SESSION['error'][] = "Wrong password";
            return false;
        }

        self::$id = $user->id;
        self::$isLogged = true;

        self::_setSession();
        return true;
    }


    public function loginAttempt($email){
        $q = "INSERT INTO login_attempt (ID, email, ip, time) VALUES (NULL , ?, ?, NOW())";
        $query = $this->con->prepare($q);
        return $query->execute(array($email, $_SERVER['REMOTE_ADDR']));
    }



    private static function _setSession(){
        if (session_id() == '') {
            session_start();
        }
        $_SESSION['auth_id'] = self::$id; //$this->id;
        $_SESSION['isLogged'] = self::$isLogged;
        $_SESSION['session_time_created'] = time(); //time the session is created
    }

    public static function _initUser(){
        if (session_id() == '') {
            session_start();
        }
        self::$id = $_SESSION['auth_id'] ?? null;
        self::$isLogged = $_SESSION['isLogged'] ?? false;
    }

    public static function logout() {
        self::$isLogged = false;
        if (session_id() == "") {
            session_start();
        }
        $_SESSION['isLogged'] = false;
        foreach ($_SESSION as $key => $value) {
            $_SESSION[$key] = "";
            unset($_SESSION[$key]);
        }
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $cookieParameters = session_get_cookie_params();
            setcookie(session_name(), '', time() - 28800,
                $cookieParameters['path'], $cookieParameters['domain'],
                $cookieParameters['secure'], $cookieParameters['httponly']);
        } //end if
        session_destroy();
    } //end function logout


    public function sessionExpired($time = 86400){
        if(isset($_SESSION['session_time_created']) &&
            time() - $_SESSION['session_time_created'] > $time){
            $this->logout();
        }
    }


    public function emailPass($email) {

        $q = "SELECT id, email FROM users WHERE email = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($email));

        if($query->rowCount() !== 1) return false;

        $query->setFetchMode(PDO::FETCH_CLASS, "user");
        $user = $query->fetch();

        if (!$user) {
            $_SESSION['error'][] = "User not found";
            return false;
        }

        $hash = uniqid(rand(1000000, 9999999), true);
        //$safeHash = $mysqli->real_escape_string($hash);
        $insertQuery = "INSERT INTO resetpassword (email_id, pass_key, date_created, status) VALUES (?, ?, NOW(), 'A')";
        $query = $this->con->prepare($insertQuery);

        if (!$query->execute(array($user->id, $hash))) {
            error_log("Problem inserting resetPassword info for " . $user->id);
            $_SESSION['error'][] = "Unknown problem";
            return false;
        }
        $urlHash = urlencode($hash);
        $fullURL = "<a href='" . Config::getConfig('app.url') . "/reset/index.php?user=$urlHash'><b>Please click here to reset your password</b></a><br><br>\r\n";
        //set up things related to the e-mail
        $to = $user->email;
        $subject = "Password Reset for 3Delacto MyDictionary";
        $message = "<html><head><title>3Delacto MyDictionary Password Request</title></head><body><div style='text-align: center'>\r\n";
        $message .= "<img style='vertical-align: middle;margin:auto;' src='https://www.dictionary.papucraft.com/images/logo_w.svg' alt='logo' width=100 height='auto'>\r\n";
        $message .= "<h3 style='color: darkblue;padding:1em;vertical-align: middle;border-radius: 6px'>Password reset requested for MyDictionary.</h3><br>\r\n";
        $message .= "<h4 style='background-color: darkblue;color:white;border-radius:6px'>" . $fullURL . "</h4> \r\n";
        $message .= "<p>Or if the link above is not working then use the following url: 
        <i>" . Config::getConfig('app.url') . "/reset/index.php?user=$urlHash</i></p>
        <hr><small>If you didn't requested the link from this message, please ignore it.</small><br><div style='background-color: darkblue;height: 2em'></div>\r\n";
        $message .= "</div></body></html>";
        $headers = "From: 3Delacto MyDictionary <pasword-reset@dictionary.papucraft.com>" . "\r\n";
        $headers .="Reply-To: pasword-reset@dictionary.papucraft.com" . "\r\n";
        $headers .="MIME-Version: 1.0" . "\r\n";
        $headers .="Content-Type: text/html; charset=UTF-8";
        mail($to,$subject,$message,$headers);
        return true;
    } //end function emailPass



    public function validateReset($formInfo) {
        $pass1 = $formInfo['password1'];
        $pass2 = $formInfo['password2'];
        if ($pass1 != $pass2) {
            $this->errorType = "nonfatal";
            $_SESSION['error'][] = "Passwords don't match";
            return false;
        }

        $hash = urldecode($formInfo['hash']);
        $email = $formInfo['email'];

        $q = "SELECT u.id as id, u.email as email FROM users u, resetpassword r WHERE 
              r.status = 'A' AND r.pass_key = ? AND u.email = ? AND u.id = r.email_id";
        $query = $this->con->prepare($q);
        $result = $query->execute(array($hash, $email));

        if (!$result) {
            $_SESSION['error'][] = "Unknown Error";
            $this->errorType = "fatal";
            error_log("database error: " . $formInfo['email'] . " - " . $formInfo['hash']);
            return false;

        } else if ($query->rowCount() == 0) {
            $_SESSION['error'][] = "Link not active or user not found";
            $this->errorType = "fatal";
            error_log("Link not active: " . $formInfo['email'] . " - " . $formInfo['hash']);
            return false;
        } else {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            if ($this->_resetPass($id, $pass1)) {
                return true;
            } else {
                $this->errorType = "nonfatal";
                $_SESSION['error'][] = "Error resetting password";
                error_log("Error resetting password: " . $id);
                return false;
            }
        }
    } //end function validateReset

    private function _resetPass($id, $pass) {

        $newPass = password_hash($pass, PASSWORD_DEFAULT);
        $q = "UPDATE users SET password = ? WHERE id = ?";
        $query = $this->con->prepare($q);

        return $query->execute(array($newPass, $id));

    } //end function _resetPass
}