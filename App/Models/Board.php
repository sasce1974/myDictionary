<?php


namespace App\Models;


use Core\Model;

class Board extends Model
{

    public $id;
    public $name;
    public $students = [];

    public function init($board){
        $this->id = $board->id;
        $this->name = $board->name;
        $this->students = $this->students();
    }

    private function students(){
        $student = new Student();
        return $student->where('board_id', $this->id);
    }

}