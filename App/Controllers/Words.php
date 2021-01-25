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

        if(!$u) die("NO USER");
        $languages = ['Japanese', 'French'];
        $word = new Word();
        //$words = $u->words();
        $words = $word->where('user_id', $u->id);
        //$words = $word->some(150);
        //var_dump($words); exit();
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
}