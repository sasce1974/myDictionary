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
    private $password;
    public $name;
    public $language;
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
        $this->language = $user->language;
        $this->language_id = $user->language_id;
        //$this->team_id = $user->team_id;
        $this->ip = $user->ip;
        $this->about = $user->about;
        $this->created_at = $user->created_at;
        $this->modified_at = $user->modified_at;
        $this->deleted_at = $user->deleted_at;
    }

    public function words(){
        $word = new Word();
        return $word->where('user_id', $this->id);
    }

    public function groups(){
        /*$q = "SELECT g.* FROM `groups` g LEFT JOIN groups_users gu
                ON gu.group_id = g.id WHERE gu.user_id = {$this->id}";*/

        $q = "SELECT g.* FROM `groups` g, groups_users gu WHERE
                gu.user_id = {$this->id} AND gu.group_id = g.id";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Group');
    }

    public function group($id){
        $q = "SELECT g.* FROM `groups` g, groups_users gu 
                WHERE gu.group_id = g.id AND gu.user_id = {$this->id} AND g.id = ?";
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
        $email = filter_var($newUserData['email']);
        $name = filter_var($newUserData['name']);
        $query->execute(array($email, $safePassword, $name, $_SERVER['REMOTE_ADDR']));
        if($query->rowCount() === 1){
            return true;
        }
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
            return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Language');
        }
        return null;
    }

    public function refresh(){
        $this->init(Auth::user());
    }

}