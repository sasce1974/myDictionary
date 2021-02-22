<?php


namespace App\Controllers\Auth;


use App\Models\Auth;
use App\Models\User;
use Core\Controller;
use Core\View;

class Login extends Controller
{

    public function indexAction(){
//        View::render('Login/index.php');
        View::render('/regorlog.php');
    }


    public function newAction(){
        if (!isset($_POST['submit'])) {
            header("Location: /login");
            exit(403);
        }

        $_SESSION["formAttempt"] = true;
        $_SESSION['error'] = array();

        $required = array("email", "password");
        foreach ($required as $requiredField) {
            if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
                $_SESSION["error"][] = $requiredField . " required.";
            }
        }
//check if submitted token is valid...
        $token = null;
        if(isset($_SESSION['token'])) $token = $_SESSION['token'];
        if (!isset($_POST['init']) || $_POST['init'] !== $token) {
            $_SESSION['error'][] = "Error in the submitted data, please try again.";
            unset($_SESSION['token']);
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "Invalid email address";
        }
// the next code retrieve login attempt with ip
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $auth = new Auth();
        $auth->loginAttempt($email);

        if (count($_SESSION["error"]) > 0) {
            header("Location: /login");
            exit();
        } else {
            //sleep(1);
            if ($auth->authenticate($email, $_POST['password'])){
                $user = Auth::user();
                if($user->deleted_at !== null){
                    $_SESSION['error'] = "<b>Your account is been blocked.</b><br> 
                    Possible reason is if you requested a deletion of your account 
                    or it is suspended by the admin. Please contact admin for any request.";
                    header("Location: /login");
                }
                unset($_SESSION['formAttempt'], $_SESSION['error']);
                header("Location: /"); //to index.php
                exit(200);
            }else {
                $_SESSION['error'] = "Wrong email or password.";
                header("Location: /login");
                exit(403);
            }
        }
    }


    public function logout(){
        Auth::logout();
        header("Location: /login");
        exit();
    }

}