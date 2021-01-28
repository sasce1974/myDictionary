<?php


namespace App\Models;


use Core\Model;

class Team extends Model
{
    public $id;
    public $name;
    public $about;
    public $created_at;
    public $updated_at;

    public function init($team){
        $this->id = $team->id;
        $this->name = $team->name;
        $this->about = $team->about;
        $this->created_at = $team->created_at;
        $this->updated_at = $team->updated_at;
    }

    public function users(){
        $user = new User();
        return $user->where('team_id', $this->id);
    }

    public function nameExist ($name){
        $query = $this->con->prepare("SELECT COUNT(id) FROM teams WHERE name = ?");
        $query->execute(array($name));

        if($query->fetchColumn() > 0) return true;

        return false;
    }

    /**
     * Create new team
     *
     * On success will return id of the new team
     *
     * @param $name
     * @param null $about
     * @return bool|string
     */
    public function save($name, $about=null){
        $query = $this->con->prepare("INSERT INTO teams ('name', 'about') VALUES (?, ?)");
        if($query->execute(array($name, $about))){
            if($query->rowCount() === 1){
                return $this->con->lastInsertId();
            }
        }
        return false;
    }
}