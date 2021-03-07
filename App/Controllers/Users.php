<?php


namespace App\Controllers;

use App\Models\Auth;
use App\Models\User;
use Core\Controller;
use Core\View;
use http\Header;

class Users extends Controller
{

    public function indexAction(){
        if(Auth::id() == 1){
            $users = new User();
            $users = $users->all(['id', 'name', 'email', 'ip', 'created_at'], 'object');
            View::render('Users/index.php', ['users'=>$users]);
        }else{
            header("Location:/");
            exit(200);
        }

    }

    public function accountAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        if($id === Auth::id() || Auth::user()->isAdmin()){
            $user = new User();
            $user = $user->find($id);
        }else{
            $user = Auth::user();
        }
        View::render('/Users/edit.php', ['user'=>$user]);
    }


    public function update(){
        $user = Auth::user();
        $_SESSION['error'] = array();

        if(isset($_POST['token']) && $_POST['token'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Wrong parameters. User not updated.";
            header("Location: /users/account");
            exit(500);
        }

        $required= array("email", "name");
        foreach ($required as $requiredField) {
            if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
                $_SESSION["error"][] = $requiredField . " required.";
            }
        }

        if (!preg_match("/^[a-zA-Z .]+$/", $_POST["name"])) {
            $_SESSION["error"][] = "Name and surname can contain only letters.";
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "Invalid email address";
        }

        if (count($_SESSION["error"]) > 0) {
            header("Location: /users/" . $user->id . "/account");
            exit(400);

        } else {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
            $about = filter_var($_POST['about'], FILTER_SANITIZE_STRING);
            $ip = $_SERVER['REMOTE_ADDR'];
            $data = ['email'=>$email, 'name'=>$name, 'phone'=>$phone, 'about'=>$about, 'ip'=>$ip];

            if($user->update($data)){
                $_SESSION['message'] = "User info updated";
                http_response_code(200);
            }else{
                $_SESSION['error'][] = "There was some error. User info not updated";
                http_response_code(500);
            }
        }
        header("Location: /users/" . $user->id . "/account");
        exit();
    }

    public function destroyAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);

        if($id == Auth::id() || Auth::id() == 1) {

            if (isset($_POST['token']) && $_POST['token'] !== $_SESSION['token']) {
                $_SESSION['error'][] = "Wrong parameters. User not deleted.";
                header("Location: /");
                exit(500);
            }

            $user = new User();
            $user = $user->find($id);
            //todo implement soft delete
            //issues: owned groups, words, delete from groups...
            if($user){
                //remove user id from groups...
                $user->removeFromAllGroups();
                if($user->delete()){
                    $_SESSION['message'] = "User deleted";
                    http_response_code(200);
                }else{
                    $_SESSION['error'][] = "There was some error. User not deleted.";
                    http_response_code(500);
                }
            }else{
                throw new \Exception("User $id not found");
            }
        }else{
            throw new \Exception("User " . Auth::id() . " made unsuccessful attempt to delete user $id");
        }
        header("Location:" . $_SERVER['HTTP_REFERER']);
        exit();
    }
}