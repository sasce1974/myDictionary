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
     * @return \App\Models\User|false
     */
    public function user(){
        $user = new User();
        return $user->find($this->user_id);
        //return $user->where('id', $this->user_id);
    }
    

    /**
     * return array of instances of word class for the current user
     *
     * @param $string
     * @return array
     */
    public function search($string){
        $user = Auth::user();
        $q = "SELECT * FROM words WHERE (lang1 LIKE ? OR lang2 LIKE ?) AND 
            (user_id = ? OR user_id IN 
                (SELECT DISTINCT user_id FROM groups_users WHERE group_id IN 
                    (SELECT group_id FROM groups_users WHERE user_id = ? AND group_invite_hash IS NULL))) 
            AND language_id = ? ORDER BY created_at DESC LIMIT 150";
        $query = $this->con->prepare($q);
        $query->execute(array("%$string%", "%$string%", $user->id, $user->id, $user->language_id));
        //$query = $this->con->query($q);
//        return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        return $query->fetchAll(PDO::FETCH_OBJ);

    }

    public function limitWords($limit = 20){
        $user = Auth::user();
        $q = "SELECT * FROM words WHERE (user_id = ? OR user_id IN 
                (SELECT DISTINCT user_id FROM groups_users WHERE group_id IN 
                    (SELECT group_id FROM groups_users WHERE user_id = ? AND group_invite_hash IS NULL)))
                AND language_id = ? ORDER BY created_at DESC LIMIT ?";
        $query = $this->con->prepare($q);
        $query->execute(array($user->id, $user->id, $user->language_id, $limit));
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function show($id){
        return $this->find($id);
    }

    public function store($lang1, $lang2){
        $user = Auth::user();
        $lang1 = ucfirst($lang1);
        $lang2 = ucfirst($lang2);
        $q="INSERT INTO words (lang1, lang2, user_id, language_id) VALUES (?, ?, ?, ?)";
        $query = $this->con->prepare($q);
        if($query->execute(array($lang1, $lang2, $user->id, $user->language_id))){
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
        $q="UPDATE words SET lang1=?, lang2=? WHERE id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        if($query->execute(array($newData['lang1'], $newData['lang2'], $id, $user->id))){
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

    /*public function truncateAllWords(){
        $q = "SELECT id, lang1, lang2 FROM words";
        $query = $this->con->query($q);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $record){
            $lang1 = trim($record['lang1']);
            $lang2 = trim($record['lang2']);
            $id = $record['id'];
            $query = $this->con->prepare("UPDATE words SET lang1 = ?, lang2 = ? 
                                                WHERE id = ?");
            $query->execute(array($lang1, $lang2, $id));
        }

        var_dump($result); exit();
    }*/

    public function countApiCalls(){
        $query = $this->con->query("SELECT COUNT(id) FROM word_api_calls
            WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 day) AND NOW()");
        return $query->fetchColumn();
    }

    public function saveApiCall($word){
        $query = $this->con->prepare("INSERT INTO word_api_calls (word, user) VALUES (?,?)");
        return $query->execute(array($word, Auth::id()));
    }


    public function wordInfo($word){



        //$encoded_url_word = urlencode($word);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wordsapiv1.p.rapidapi.com/words/" . $word,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: wordsapiv1.p.rapidapi.com",
                "x-rapidapi-key: 74dd5a2a37msh376da61d68fb3dcp137a1djsnf76402615809"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            http_response_code(500);
            throw new \Exception("Error retrieving info for \'$word\'");
            //return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }


}