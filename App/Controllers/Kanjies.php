<?php


namespace App\Controllers;


use App\Models\Auth;
use App\Models\Kanji;
use Core\Controller;
use Core\View;

class Kanjies extends Controller
{
    public function indexAction(){

        if(Auth::check()){
            $user_id = Auth::id();
        }else{
            header("Location: /login");
            exit();
        }

        $kanji = new Kanji();

        $kanji_list = $kanji->allMy($user_id);

        $cat = [];

        foreach ($kanji_list as $k){
            $cat[$k->n_level][] = $k;
        }
        $kanji_list = $cat;
        header('Content-type: text/html; charset=utf-8');

        View::render('Words/kanji.php', ['kanji_list'=>$kanji_list]);
    }

    public function store(){
        $data = [];
        $data['sign'] = filter_input(INPUT_POST, 'sign', FILTER_SANITIZE_STRING);
        $data['meaning'] = filter_input(INPUT_POST, 'meaning', FILTER_SANITIZE_STRING);
        $data['on_reading'] = filter_input(INPUT_POST, 'on_reading', FILTER_SANITIZE_STRING);
        $data['kun_reading'] = filter_input(INPUT_POST, 'kun_reading', FILTER_SANITIZE_STRING);
        $data['n_level'] = filter_input(INPUT_POST, 'n_level', FILTER_SANITIZE_NUMBER_INT, ['min'=>1, 'max'=>5]);
        $data['user_id'] = Auth::id();

        $kanji = new Kanji();

        if($kanji->store($data)){
            $_SESSION['message'] = "Kanji inserted";
        }else{
            $_SESSION['error'] = "Error! Data not inserted";
        }

        return header("Location: /kanjies");
    }

    public function destroyAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $kanji = new Kanji();
        if($kanji->delete($id)){
            $_SESSION['message'] = "Kanji deleted";
        }else{
            $_SESSION['error'] = "Error! Kanji not deleted";
        }
        return header("Location: /kanjies");
    }
}