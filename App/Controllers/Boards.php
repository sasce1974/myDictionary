<?php


namespace App\Controllers;


use App\Models\Board;
use Core\Controller;
use Core\View;

class Boards extends Controller
{
    public function indexAction(){
        if(isset($this->route_params['id'])){ //if board id is added to the route, show that board
            $this->studentsAction();
        }else {
            View::render('Boards/index.php');
        }
    }

    public function studentsAction(){
        //if there is a student id given in the route, redirect to students controller, method show
        if(isset($this->route_params['aid'])){
            header('Location: /students/' . $this->route_params['aid']);
            exit();
        }else {

            $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min' => 1]);
            $board = new Board();
            $board = $board->find($id);
            View::render("Boards/board.php", [
                'board' => $board,
                'students' => $board->students
            ]);
        }
    }

}