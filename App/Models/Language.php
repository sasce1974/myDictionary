<?php


namespace App\Models;


use Core\Model;
use PDO;

class Language extends Model
{
    public $id;
    public $name;
    public $created_by;
    public $created_at;

    public function init($language){
        $this->id = $language->id;
        $this->name = $language->name;
        $this->created_by = $language->created_by;
        $this->created_at = $language->created_at;
    }

    public function words(){
        $word = new Word();
        return $word->where('language_id', $this->id);
    }

    public function users(){
        $q = "SELECT u.* FROM users u, users_languages ul WHERE ul.language_id = ? AND ul.user_id = u.id";
        $query = $this->con()->prepare($q);
        if($query->execute(array($this->id))){
            return $query->fetchAll(PDO::FETCH_CLASS, 'User');
        }
        return null;
    }

    public function checkIfExist(int $user_id, int $language_id){
        //$user_id = Auth::id();
        $q = "SELECT COUNT(id) FROM users_languages WHERE language_id = ? AND user_id = ?";
        $query = $this->con()->prepare($q);
        if($query->execute(array($language_id, $user_id))) return $query->fetchColumn();
        return false;
    }


    public function save($id){
        $user = Auth::user();

        if($this->checkIfExist($user->id, $id) == 0){
            $q = "INSERT INTO users_languages (user_id, language_id) VALUES (?, ?)";
            $query = $this->con()->prepare($q);
            $new_id = $query->execute(array($user->id, $id));
            if($new_id) {
                //set user to new language
                $user->setLanguage($id);
                return true;
            }
        }
        return false;
    }

    public function delete($id){
        $user = Auth::user();
        $q = "DELETE FROM users_languages WHERE language_id = ? AND user_id = ?";

        $query = $this->con()->prepare($q);
        if($query->execute(array($id, $user->id))){
            $user->setLanguage(null);
            return $query->rowCount();
        } 

        return false;
    }
}