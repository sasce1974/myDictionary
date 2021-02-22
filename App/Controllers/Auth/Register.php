<?php


namespace App\Controllers\Auth;


use App\Models\User;
use Core\Controller;
use Core\View;

class Register extends Controller
{
    public function indexAction(){
//        View::render('/Register/index.php');
        View::render('/regorlog.php');
    }

    public function store(){

        $user = new User();

        $_SESSION["formAttempt"] = true;
        $_SESSION['error'] = array();

        $required= array("email", "password1", "password2", "name");
        foreach ($required as $requiredField) {
            if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
                $_SESSION["error"][] = $requiredField . " required.";
            }
        }
        //check if submitted token is valid...
        if(!isset($_POST['init']) || $_POST['init'] !== $_SESSION['token']){
            $_SESSION['error'] = "Error in the submitted data, please try again.";
            unset($_SESSION['token']);
        }

        if (!preg_match("/^[a-zA-Z .]+$/", $_POST["name"])) {
            $_SESSION["error"][] = "Name and surname can contain only letters.";
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "Invalid email address"; }

        if ($_POST["password1"] != $_POST["password2"]) {
            $_SESSION["error"][] = "Passwords doesn't match";
        }

        if($user->checkIfEmailExist($_POST['email'])){
            $_SESSION["error"][] = "Account with the same e-mail already exist.";
        }


        if (count($_SESSION["error"]) > 0) {
            header("Location: /register/?email={$_POST['email']}&name={$_POST['name']}");
            exit(400);
        } else {
            //save new user
            $email = $_POST['email'];

            //check if the


            $name = $_POST['name'];
            $password = $_POST['password1'];
            $newUserData = ['email'=>$email, 'name'=>$name, 'password'=>$password];

            if($user->store($newUserData)){
                unset($_SESSION["formAttempt"]);
                $_SESSION['message'] = "User {$name} registered. Please Log in to continue.";
                header("Location: /login");
                //todo send activate account email?
                exit (200);
            }else{
                error_log("Problem with the registration: {$email}");
                $_SESSION["error"][] = "There was some problem with the registration, please try again.";
                header("Location: /register");
                exit(500);
            }
        }
    }


    public function checkEmailExist(){
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $user = new User();
        if($user->checkIfEmailExist($email)){
            print "<span style='color:#f55;font-size:80%;'>This email is taken &nbsp;&#x2718;</span>";
        }else{
            print "<span style='color:#3b3;font-size:80%;'>This email is available &nbsp;&#x2714;</span>";
        }
    }

}