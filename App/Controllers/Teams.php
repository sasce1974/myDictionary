<?php


namespace App\Controllers;


use App\Models\Team;
use Core\Controller;
use Core\View;

class Teams extends Controller
{

    public function create(){
        View::render('Teams/create.php');
    }

    public function store(){
        $_SESSION['error'] = array();

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        if(strlen($name < 1)) $_SESSION['error'][] = "Team name should be at least 1 character long";

        $team = new Team();
        if($team->nameExist($name)) $_SESSION['error'][] = "Team with name $name already exist. Please choose another team name";

        if(count($_SESSION['error']) > 0){
            header("Location: /teams/create");
            exit(403);
        }

        $about = filter_input(INPUT_POST, 'about', FILTER_SANITIZE_STRING);

        if($id = $team->save($name, $about)){
            $_SESSION['success'] = "Team created";
            header("Location: /teams/$id/show"); exit(200);
        }else{
            $_SESSION['error'][] = "Unknown error. Team not created.";
            header("Location: /teams/create"); exit(500);
        }

    }
}