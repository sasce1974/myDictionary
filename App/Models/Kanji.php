<?php


namespace App\Models;


use Core\Model;
use mysql_xdevapi\Exception;
use PDO;

class Kanji extends Model
{
    public $id;
    public $user_id;
    public $sign;
    public $meaning;
    public $on_reading;
    public $kun_reading;
    public $n_level;
    public $created_at;
    public $updated_at;

    protected $table = 'kanji';

    public function init($kanji){
        $this->id = $kanji->id;
        $this->user_id = $kanji->user_id;
        $this->sign = $kanji->sign;
        $this->meaning = $kanji->meaning;
        $this->on_reading = $kanji->on_reading;
        $this->kun_reading = $kanji->kun_reading;
        $this->n_level = $kanji->n_level;
        $this->created_at = $kanji->created_at;
        $this->updated_at = $kanji->updated_at;
    }

    public function user(){
        $user = new User();
        return $user->find($this->user_id);
    }

    public function allMy($user_id){
        try{
        //$q = "SELECT * FROM kanji WHERE user_id = {$user_id}";
            $q = "SELECT * FROM kanji WHERE (user_id = ? OR user_id IN
            (SELECT DISTINCT user_id FROM groups_users WHERE group_id IN
            (SELECT group_id FROM groups_users WHERE user_id = ? AND group_invite_hash IS NULL)))";
            $this->con->exec('set names utf8');
            $query = $this->con->prepare($q);
            $query->execute(array($user_id, $user_id));
            return $query->fetchAll(PDO::FETCH_OBJ);
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function store (array $data){
        $this->con->exec('set names utf8');
        $q = "INSERT INTO kanji (user_id, sign, meaning, on_reading, 
                   kun_reading, n_level) VALUES (?,?,?,?,?,?)";
        $query = $this->con->prepare($q);
        if($query->execute(array($data['user_id'], $data['sign'], $data['meaning'],
            $data['on_reading'], $data['kun_reading'], $data['n_level']))){
            if($query->rowCount() === 1){
                return true;
            }else{
                return false;
            }
        }
    }

    public function show($id){
        $q = "SELECT * FROM kanji WHERE id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($id));
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function delete($id){
        //$q = "SELECT id, user_id FROM kanji WHERE id = ?";
        //$query = $this->con->prepare($q);
        //$query->execute(array($id));
        $kanji = $this->show($id); //$query->fetch(PDO::FETCH_OBJ);
        if($kanji){
            if(Auth::id() == $kanji->user_id){
                $query = $this->con->exec("DELETE FROM kanji where id = {$kanji->id}");
                if($query == 1) return true;
            }else{
                throw new Exception("Not Authorized", 403);
            }
        }
        return false;
    }
}