<?php


namespace App\Models;


use App\Models\Auth;
use Core\Error;
use Core\Model;
use PDO;
use PDOException;
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

    public function isOwner($group_id){
        try {
            $query = $this->con->prepare("SELECT COUNT(id) FROM `groups` WHERE id = ? 
                                        AND owner_id = ?");
            $query->execute(array($group_id, Auth::id()));
            if ($query->fetchColumn() === 1) return true;
            return false;
        }catch (PDOException $e){
            print $e->getMessage();
        }catch (\Exception $e){
            print $e->getMessage();
        }
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
    public function create($name, $country, $city, $about=null){
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

    public function update($group_id, $name, $country, $city, $about=null){
        try {
            $owner_id = Auth::id();
            $q = "UPDATE groups SET name=?, country=?, city=?, about=? WHERE id=? AND owner_id = ?";
            $query = $this->con->prepare($q);
            if ($query->execute(array($name, $country, $city, $about, $group_id, $owner_id))) {
                if ($query->rowCount() === 1) return true;
            }
            return false;
        }catch (PDOException $e){
            print $e->getMessage();
        }catch (\Exception $e){
            print $e->getMessage();
        }
    }


    public function delete($group_id){
        try {
            $owner_id = Auth::id();
            $q = "DELETE FROM `groups` WHERE id = ? and owner_id = ?";
            $q1 = "DELETE FROM groups_users WHERE group_id = ?";
            $query = $this->con->prepare($q);
            $query->execute(array($group_id, $owner_id));
            if ($query->rowCount() === 1) {
                $query = $this->con->prepare($q1);
                $query->execute(array($group_id));
                return true;
            }
            return false;
        }catch (PDOException $e){
            print $e->getMessage();
        }catch (\Exception $e){
            print $e->getMessage();
        }
    }


    /*GROUP MEMBERS */


    /**

     *
     * @return array of stdClass of all users - members of this group
     */
    public function members(){
        $q = "SELECT u.id, u.name, u.email FROM users u, groups_users gu WHERE 
            gu.group_id = '$this->id' AND gu.user_id = u.id AND gu.group_invite_hash IS NULL 
            ORDER BY gu.id";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\User');
    }

    public function invitedMembers(){
        $q = "SELECT u.id, u.name, u.email FROM users u, groups_users gu WHERE 
            gu.group_id = '$this->id' AND gu.user_id = u.id AND gu.group_invite_hash IS NOT NULL 
            ORDER BY gu.id";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\User');
    }

    /**
     * Number of members of this group
     *
     * @return mixed (int)
     */
    public function countMembers(){
        $query = $this->con->query("SELECT COUNT(u.id) FROM users u, groups_users gu WHERE 
            gu.group_id = '$this->id' AND gu.user_id = u.id AND gu.group_invite_hash IS NULL");
        return $query->fetchColumn();
    }

    public function invite($user_name, $email){

    }

    public function makeInviteHash($group_id, $user_id, $hash){

        $q = "INSERT INTO groups_users (group_id, user_id, group_invite_hash) VALUES (?, ?, ?)";
        $query = $this->con->prepare($q);
        $query->execute(array($group_id, $user_id, $hash));
        if($query->rowCount() === 1) return true;
        return false;
    }

    public function clearInviteHash($hash){
        $q = "SELECT * FROM groups_users WHERE group_invite_hash = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($hash));
        $r = $query->fetch(PDO::FETCH_OBJ);
        $query1 = $this->con->prepare("UPDATE groups_users SET group_invite_hash = null 
                WHERE id = ?");
        $query1->execute(array($r->id));
        if($query1->rowCount() === 1) return $r;
        return false;
    }

    public function checkIfUserIsInGroup($user_id, $group_id){
        $q = "SELECT COUNT(id) FROM groups_users WHERE user_id=? AND group_id=?";
        $query = $this->con->prepare($q);
        $query->execute(array($user_id, $group_id));
        if($query->fetchColumn() > 0) return true;
        return false;
    }
}