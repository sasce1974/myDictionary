<?php


namespace App\Controllers;
use App\Models\Auth;
use App\Models\User;
use App\Models\Word;
use Core\Controller;
use Core\View;


class Words extends Controller
{
    public function indexAction(){
        if(Auth::check()){
            $u = Auth::user();
        }else{
            $u = new User();
        }

        $languages = $u->languages();
        //$languages = ['Japanese', 'French'];
        $word = new Word();
        $words = $word->last20words($u);

        View::render('Words/index.php', [
            'user'=>$u,
            'words'=>$words,
            'languages'=>$languages,
            'chosen_language'=>$u->language
        ]);
    }

    /**
     * prints json encoded collection of words
     */
    public function searchAction(){
        $string = filter_input(INPUT_GET, 'string', FILTER_SANITIZE_STRING);
        $word = new Word();
        if(!empty($string)){
            print json_encode($word->search($string));
        }
    }

    public function changeLanguageAction(){
        if(isset($_POST['chosen_language']) && $_POST['chosen_language'] !=""){
            $language = filter_input(INPUT_POST, 'chosen_language', FILTER_SANITIZE_STRING);
            if(Auth::check()){
                Auth::user()->language($language);
            }

        }
        return header("Location: /");
    }

    public function show(){
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $word = new Word();
        print json_encode($word->show($id));
    }


    public function updateAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        $lang1 = filter_input(INPUT_POST, 'lang1', FILTER_SANITIZE_STRING);
        $lang2 = filter_input(INPUT_POST, 'lang2', FILTER_SANITIZE_STRING);
        $language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING);
        $newData = ['lang1'=>$lang1, 'lang2'=>$lang2, 'language'=>$language];
        if(isset($_SESSION['token']) && $_SESSION['token'] == $token) {
            $word = new Word();
            if ($word->update($id, $newData)) {
                $_SESSION['message'] = "Record updated";
                http_response_code(200);
            } else {
                $_SESSION['error'] = "Record not updated";
                http_response_code(500);
            }
        }else{
            $_SESSION['error'] = "Wrong parameters submitted";
            http_response_code(500);
        }
        header("Location: /");
        exit();
    }

    public function storeAction(){
        $lang1 = filter_input(INPUT_POST, 'lang1', FILTER_SANITIZE_STRING);
        $lang2 = filter_input(INPUT_POST, 'lang2', FILTER_SANITIZE_STRING);
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        if(isset($_SESSION['token']) && $_SESSION['token'] == $token) {
            $word = new Word();
            if ($word->store($lang1, $lang2)) {
                $_SESSION['message'] = "Record created";
                http_response_code(200);
            } else {
                $_SESSION['error'] = "Record not created";
                http_response_code(500);
            }
        }else{
            $_SESSION['error'] = "Wrong parameters submitted";
            http_response_code(500);
        }
        header("Location: /");
        exit();
    }


    public function deleteAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
//        $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
//        if(isset($_SESSION['token']) && $_SESSION['token'] == $token) {
            $word = new Word();
            if ($word->delete($id)) {
                $_SESSION['message'] = "Record deleted";
                http_response_code(200);
            } else {
                $_SESSION['error'] = "Record not deleted";
                http_response_code(500);
            }
        /*}else{
            $_SESSION['error'] = "Wrong parameters received";
            http_response_code(500);
        }*/
        header("Location: /");
        exit();
    }
}