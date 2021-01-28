<?php


namespace App\Controllers;


use App\Models\Language;
use Core\Controller;

class Languages extends Controller
{
    public function storeAction(){
        $_SESSION['error'] = array();
        $id = trim(filter_input(INPUT_POST, 'newLanguage', FILTER_SANITIZE_NUMBER_INT));
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        if(isset($_SESSION['token']) && $_SESSION['token'] === $token && !empty($id)){
            $language = new Language();
            if($language->save($id)){
                $_SESSION['message'] = "Language created";
                http_response_code(200);
            }else{
                $_SESSION['error'][] = "There was some error. Language not created";
                http_response_code(500);
            }
        }else{
            $_SESSION['error'][] = "Wrong parameters submitted";
            http_response_code(500);
        }
        header("Location:/");
        exit();
    }


    public function destroyAction(){
        $_SESSION['error'] = array();
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        //$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
        if(isset($_SESSION['token']) && $_SESSION['token'] === $token){
            $language = new Language();
            if($language->delete($id)){
                $_SESSION['message'] = "Language deleted";
                http_response_code(200);
            }else{
                $_SESSION['error'][] = "There was some error. Language not deleted";
                http_response_code(500);
            }
        }else{
            $_SESSION['error'][] = "Wrong parameters submitted";
            http_response_code(500);
        }
        header("Location:/");
        exit();
    }
}