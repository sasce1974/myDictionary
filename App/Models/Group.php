<?php


namespace App\Models;


use Core\Error;
use Core\Model;
use PDO;
use App\Models\Auth;
use App\Models\User;

class Group extends Model
{
    public $id;
    public $owner_id;
    public $name;
    public $country;
    public $city;
    public $about;
    public $created_at;
    public $updated_at;

    public function init($group){
        $this->id = $group->id;
        $this->owner_id = $group->owner_id;
        $this->name = $group->name;
        $this->country = $group->country;
        $this->city = $group->city;
        $this->about = $group->about;
        $this->created_at = $group->created_at;
        $this->updated_at = $group->updated_at;
    }


    public function owner(){
        $user = new User();
        return $user->find($this->owner_id);
    }

    public function nameExist ($name){
        $query = $this->con->prepare("SELECT COUNT(id) FROM groups WHERE name = ?");
        $query->execute(array($name));

        if($query->fetchColumn() > 0) return true;

        return false;
    }

    public function countries(){
        $q = "SELECT DISTINCT country FROM world_cities ORDER BY country";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countryCities($country){
        $query = "SELECT DISTINCT city FROM world_cities WHERE country = ? ORDER BY city";
        $query = $this->con->prepare($query);
        $query->execute(array($country));
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }


    public function members(){
        $q = "SELECT u.id, u.name, u.email FROM users u, groups_users gu WHERE 
            gu.group_id = '$this->id' AND gu.user_id = u.id ORDER BY gu.id";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\User');
    }

    public function countMembers(){
        $query = $this->con->query("SELECT COUNT(u.id) FROM users u, groups_users gu WHERE 
            gu.group_id = '$this->id' AND gu.user_id = u.id");
        return $query->fetchColumn();
    }

    /**
     * Create new group
     *
     * On success will return id of the new group
     *
     * @param $name
     * @param $country
     * @param $city
     * @param null $about
     * @return bool|string
     */
    public function save($name, $country, $city, $about=null){
        $owner_id = Auth::id();
        $q = "INSERT INTO groups (owner_id, name, country, city, about) VALUES (?,?,?,?,?)";
        $query = $this->con->prepare($q);
        if($query->execute(array($owner_id, $name, $country, $city, $about))){
            if($query->rowCount() === 1){
                $group_id = $this->con->lastInsertId();
                $query = $this->con->prepare("INSERT INTO groups_users (group_id, user_id) 
                        VALUES (?,?)");
                if($query->execute(array($group_id, $owner_id))) return $group_id;
            }
        }
        return false;
    }
}