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
        //todo get auth user id and implement into the query
        $q = "SELECT * FROM words WHERE lang1 LIKE ? OR lang2 LIKE ? LIMIT 150";
        $query = $this->con->prepare($q);
        $query->execute(array("%$string%", "%$string%"));
        //$query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, $this->model);

    }
}