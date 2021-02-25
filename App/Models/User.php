<?php


namespace App\Models;


use App\Config;
use Core\Model;
use App\Models\Word;
use App\Models\Language;
use PDO;

class User extends Model
{
    public $id;
    public $email;
    //private $password;
    public $name;
    public $phone;
    public $language_id;
    //public $team_id;
    public $ip;
    public $about;
    public $created_at;
    public $modified_at;
    public $deleted_at;


    public function init($user){
        $this->id = $user->id;
        $this->email = $user->email;
        //$this->password = $user->password;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->language_id = $user->language_id;
        //$this->team_id = $user->team_id;
        $this->ip = $user->ip;
        $this->about = $user->about;
        $this->created_at = $user->created_at;
        $this->modified_at = $user->modified_at;
        $this->deleted_at = $user->deleted_at;
    }


    public function isAdmin(){
        return false;
        //todo make column isAdmin in the table
    }

    public function words(){
        $word = new Word();
        return $word->where('user_id', $this->id, 'object');
    }

    public function countWords(){
        $word = new Word();
        return $word->countWhere('user_id', $this->id);
    }

    public function myGroups(){
        $q = "SELECT * FROM `groups` WHERE owner_id = {$this->id}";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Group');
    }

    public function groups(){

        $q = "SELECT g.* FROM `groups` g, groups_users gu WHERE gu.user_id = {$this->id} AND 
            gu.group_id = g.id AND gu.group_invite_hash IS NULL";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Group');
    }

    public function groupsMember(){
        $q = "SELECT g.* FROM `groups` g, groups_users gu WHERE gu.user_id = {$this->id} AND 
            gu.group_id = g.id AND gu.group_invite_hash IS NULL AND g.owner_id <> {$this->id}";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Group');
    }

    public function group($id){
        $q = "SELECT g.* FROM `groups` g, groups_users gu WHERE gu.group_id = g.id AND 
                gu.user_id = {$this->id} AND gu.group_invite_hash IS NULL AND g.id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($id));
        $query->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Group');
        return $query->fetch(PDO::FETCH_CLASS);
    }

    public function wordsPerLanguage($language_id){
        $q = "SELECT COUNT(id) FROM words WHERE language_id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($language_id, $this->id));
        return $query->fetchColumn();
    }

    /**
     * store new user in database (register)
     *
     * @param array $newUserData
     * @return bool
     */
    public function store(array $newUserData){

        $q = "INSERT INTO users (email, password, name, ip) VALUES (?, ?, ?, ?)";
        $query = $this->con->prepare($q);
        $safePassword = password_hash($newUserData['password'], PASSWORD_DEFAULT);
        $email = filter_var($newUserData['email'], FILTER_SANITIZE_EMAIL);
        $name = filter_var($newUserData['name'], FILTER_SANITIZE_STRING);
        $query->execute(array($email, $safePassword, $name, $_SERVER['REMOTE_ADDR']));
        if($query->rowCount() === 1){
            //return true;
            return $this->con->lastInsertId();
        }
        return false;
    }


    public function update(array $data){
        $q = "UPDATE users SET email=?, name=?, phone=?, about=?, ip=? WHERE id=?";
        $query = $this->con->prepare($q);
        $r = $query->execute(array($data['email'], $data['name'], $data['phone'], $data['about'], $_SERVER['REMOTE_ADDR'], $this->id));
        if($r && $query->rowCount() === 1) return true;
        return false;
    }

    public function checkIfEmailExist($email){
        $findUser = "SELECT id FROM users WHERE email = ?";
        $findResult = $this->con->prepare($findUser);
        $findResult->execute(array($email));

        $findRow = $findResult->fetch(PDO::FETCH_ASSOC);
        if (isset($findRow['id']) && $findRow['id'] != "") {
            return true;
        }
        return false;
    }


    /**
     * Set/Choose User Language
     *
     * @param $language_id
     * @return bool
     */
    public function setLanguage($language_id){
        $this->language_id = $language_id;
        $q = "UPDATE users SET language_id = ? WHERE id = ?";
        $query = $this->con->prepare($q);

        return $query->execute(array($language_id, $this->id));
    }

    /**
     * returns this user set language or false
     *
     * returned language is instance of Language class
     *
     * @return mixed
     */
    public function getLanguage(){
        $language = new Language();
        return $language->find($this->language_id);
    }


    /**
     * Get all user used languages
     *
     * @return array|null
     */

    public function languages(){
        $q = "SELECT l.* FROM languages l, users_languages ul WHERE ul.user_id = ? AND ul.language_id = l.id";
        $query = $this->con->prepare($q);
        if($query->execute(array($this->id))){
//            return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Language');
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
        return null;
    }

    public function refresh(){
        $this->init(Auth::user());
    }


    public function saveInviteHash($hash=null){
        $q = "UPDATE users SET group_invite_hash = ? WHERE id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($hash, $this->id));
        if($query->rowCount() === 1) return true;
        return false;
    }
}