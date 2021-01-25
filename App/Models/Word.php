<?php


namespace App\Models;


use Core\Model;
use App\Models\User;
use PDO;

class Word extends Model
{
    public $id;
    public $user_id;
    public $lang1;
    public $lang2;
    public $language;
    public $note;
    public $created_at;

    public function init($word){
        $this->id = $word->id;
        $this->user_id = $word->user_id;
        $this->lang1 = $word->lang1;
        $this->lang2 = $word->lang2;
        $this->language = $word->language;
        $this->note = $word->note;
        $this->created_at = $word->created_at;
    }

    /**
     * returns user - relationship
     *
     * @return array
     */
    public function user(){
        $user = new User();
        return $user->where('id', $this->user_id);
    }

    /**
     * return array of instances of word class for the current user
     *
     * @param $string
     * @return array
     */
    public function search($string){
        $q = "SELECT * FROM words WHERE (lang1 LIKE ? OR lang2 LIKE ?) AND user_id = ? LIMIT 150";
        $query = $this->con->prepare($q);
        $query->execute(array("%$string%", "%$string%", Auth::id()));
        //$query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, $this->model);

    }

    public function last20words($user){
        $q = "SELECT * FROM words WHERE user_id = ? AND language = ? ORDER BY created_at DESC LIMIT 20";
        $query = $this->con->prepare($q);
        $query->execute(array($user->id, $user->language));
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function show($id){
        return $this->find($id);
    }

    public function store($lang1, $lang2){
        $user = Auth::user();
        $q="INSERT INTO words (lang1, lang2, user_id, language) VALUES (?, ?, ?, ?)";
        $query = $this->con->prepare($q);
        if($query->execute(array($lang1, $lang2, $user->id, $user->language))){
            if($query->rowCount() === 1) return true;
        }
        return false;
    }


    /**
     * Update resource
     *
     * @param $id
     * @param $newData
     * @return bool
     */
    public function update($id, $newData){
        $user = Auth::user();
        $q="UPDATE words SET lang1=?, lang2=?, language=? WHERE id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        if($query->execute(array($newData['lang1'], $newData['lang2'], $newData['language'], $id, $user->id))){
            if($query->rowCount() === 1) return true;
        }
        return false;
    }


    /**
     * Delete resource
     *
     * @param $id
     * @return bool
     */
    public function delete($id){
        $q = "DELETE FROM words WHERE user_id = ? AND id = ?";
        $query = $this->con->prepare($q);
        return $query->execute(array(Auth::id(), $id));
    }

}