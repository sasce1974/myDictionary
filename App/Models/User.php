<?php


namespace App\Models;


use App\Config;
use Core\Model;
use App\Models\Word;
use PDO;

class User extends Model
{
    public $id;
    public $email;
    private $password;
    public $name;
    public $language;
    public $team_id;
    public $ip;
    public $about;
    public $created_at;
    public $modified_at;
    public $deleted_at;


    public function init($user){
        $this->id = $user->id;
        $this->email = $user->email;
        $this->password = $user->password;
        $this->name = $user->name;
        $this->language = $user->language;
        $this->team_id = $user->team_id;
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
     * @param $language
     * @return bool
     */
    public function language($language){
        $this->language = $language;
        $q = "UPDATE users SET language = ? WHERE id = ?";
        $query = $this->con->prepare($q);

        return $query->execute(array($language, $this->id));
    }


    /**
     * Get all user used languages
     *
     * @return array|null
     */
    public function languages(){
        $q = "SELECT DISTINCT(language) FROM words WHERE user_id = ?";
        $query = $this->con->prepare($q);
        if($query->execute(array($this->id))){
            return $query->fetchAll(PDO::FETCH_COLUMN);
        }
        return null;
    }


}