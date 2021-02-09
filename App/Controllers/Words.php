<?php


namespace App\Controllers;
use App\Models\Auth;
use App\Models\Language;
use App\Models\User;
use App\Models\Word;
use Core\Controller;
use Core\View;
use http\Client;


class Words extends Controller
{
    /*public function sharedAction(){
        $word = new Word();
        return $word->getSharingUsersId();
    }*/

    public function indexAction(){
        if(Auth::check()){
            $u = Auth::user();
        }else{
            $u = new User();
            //throw new \Exception("User Not Authenticated", 403);
        }
        $languages = $u->languages();

        $lan = new Language();
        //$word = new Word();

        //var_dump($u->groups()); exit();
        View::render('Words/index.php', [
            'user'=>$u,
            //'words'=>$word->limitWords($u, 15),
            'languages'=>$languages,
            'all_lang'=>$lan->all(['id', 'name']),
            'chosen_language'=>$u->getLanguage()
        ]);
    }

    public function mostRecentWords(){
        $user = Auth::user();
        $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
        $word = new Word();
        print json_encode($word->limitWords($user, $limit));
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
            $language_id = filter_input(INPUT_POST, 'chosen_language', FILTER_SANITIZE_NUMBER_INT);
            if(Auth::check()){
                Auth::user()->setLanguage($language_id);
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
        $lang1 = trim(filter_input(INPUT_POST, 'lang1', FILTER_SANITIZE_STRING));
        $lang2 = trim(filter_input(INPUT_POST, 'lang2', FILTER_SANITIZE_STRING));
        //$language = trim(filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING));
        $newData = ['lang1'=>$lang1, 'lang2'=>$lang2];
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
        $lang1 = trim(filter_input(INPUT_POST, 'lang1', FILTER_SANITIZE_STRING));
        $lang2 = trim(filter_input(INPUT_POST, 'lang2', FILTER_SANITIZE_STRING));
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        if(isset($_SESSION['token']) && $_SESSION['token'] == $token && !empty($lang2) && !empty($lang1)) {
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


    /*public function truncateAllWordsAction(){
        $word = new Word();
        if($r = $word->truncateAllWords()){
            return "$r records truncated";
        }else{
            return "No action was performed";
        }
    }*/

    /*public function countApiCallsAction(){
        $w = new Word();
        $r = $w->saveApiCall('test');
        print $w->countApiCalls();
    }*/

    public function getWordInfoAction(){

        $word = filter_input(INPUT_GET, 'word', FILTER_SANITIZE_STRING);
        $w = new Word();

        if($w->countApiCalls() > 2200){
            echo '["word": "Word Limit Exceeded"]';
            exit(500);
        }

        $response = "";
        try {

            $w->saveApiCall($word);

            $response = $w->wordInfo($word);

        } catch (\Exception $e) {
            print $e->getMessage();
        }

        echo $response;
    }

}