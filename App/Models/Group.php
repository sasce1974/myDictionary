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

    public function featured(){
        $q = "SELECT * FROM `groups` ORDER BY created_at DESC LIMIT 5";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Group');
    }

    public function owner(){
        $user = new User();
        return $user->find($this->owner_id);
    }

    public function nameExist ($name){

        if($this->countWhere('name', $name) > 0) return true;

        return false;
    }

    public function isOwner($group_id){
        $query = $this->con->prepare("SELECT COUNT(id) FROM `groups` WHERE id = ? 
                                    AND owner_id = ?");
        $query->execute(array($group_id, Auth::id()));
        if ($query->fetchColumn() == 1) return true;
        return false;
    }

    public function countries(){
        $q = "SELECT DISTINCT country FROM world_cities ORDER BY country";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countryCities($country){
        $query = "SELECT DISTINCT city_ascii FROM world_cities WHERE country = ? ORDER BY city";
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
        $q = "INSERT INTO `groups` (owner_id, name, country, city, about) VALUES (?,?,?,?,?)";
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

        $owner_id = Auth::id();
        $q = "UPDATE `groups` SET name=?, country=?, city=?, about=? WHERE id=? AND owner_id = ?";
        $query = $this->con->prepare($q);
        if ($query->execute(array($name, $country, $city, $about, $group_id, $owner_id))) {
            if ($query->rowCount() === 1) return true;
        }
        return false;

    }


    public function delete($group_id){
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


    /**
     * Get array of users that requested to join this group
     *
     * @return array
     */
    public function joinRequests(){
        $q = "SELECT u.id, u.name, u.email, j.* FROM users u, join_group_request j WHERE 
            j.group_id = '$this->id' AND j.user_id = u.id AND j.declined = 0 
            ORDER BY j.id";
        $query = $this->con->query($q);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }


    /**
     * Request by an user to join this group
     *
     * @param $user_id
     * @param null $message
     * @return bool
     */
    public function joinRequest($user_id, $message = null){
        $q = "INSERT INTO join_group_request (group_id, user_id, user_message) VALUES (?, ?, ?)";
        $query = $this->con->prepare($q);
        $query->execute(array($this->id, $user_id, $message));
        if($query->rowCount() == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check if there is a request by the user to join this group
     *
     * @param $user_id
     * @return bool
     */
    public function joinRequestExists($user_id){
        $q = "SELECT COUNT(id) FROM join_group_request WHERE group_id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($this->id, $user_id));
        $r = $query->fetchColumn();
        if($r > 0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * Accept the user request to join this group
     *
     * @param $user_id
     * @return bool
     */
    public function joinAccept($user_id){
        $q = "DELETE FROM join_group_request WHERE group_id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($this->id, $user_id));
        if($query->rowCount() == 1){
            $q = "INSERT INTO groups_users (group_id, user_id) VALUES (?, ?)";
            $query = $this->con->prepare($q);
            $query->execute(array($this->id, $user_id));
            if($query->rowCount() === 1) return true;
        }

        return false;
    }

    /**
     * Decline user request to join this group
     *
     * @param $user_id
     * @return bool
     */
    public function joinDecline($user_id){
        $q = "UPDATE join_group_request SET declined = 1 WHERE group_id = ? AND user_id = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($this->id, $user_id));
        if($query->rowCount() == 1){
            return true;
        }
        return false;
    }


    /**
     * Send invitation by email to user to join the group
     *
     * @param $group_id
     * @param $user_id
     * @param $hash
     * @return bool
     */
    public function makeInviteHash($group_id, $user_id, $hash){

        $q = "INSERT INTO groups_users (group_id, user_id, group_invite_hash) VALUES (?, ?, ?)";
        $query = $this->con->prepare($q);
        $query->execute(array($group_id, $user_id, $hash));
        if($query->rowCount() === 1) return true;
        return false;
    }

    /**
     * Clear the invitation hash after the user accepts invitation or invitation is been revoked
     *
     * @param $hash
     * @return bool|mixed
     */
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

    public function hasUser($user_id){
        $q = "SELECT COUNT(id) FROM groups_users WHERE user_id=? AND group_id=?";
        $query = $this->con->prepare($q);
        $query->execute(array($user_id, $this->id));
        if($query->fetchColumn() > 0) return true;
        return false;
    }

    public function removeUser($user_id){
        //check if auth user is owner or user is the auth
        $auth_id = Auth::id();
        if($this->owner_id == $auth_id || $user_id == $auth_id){
            $q = "DELETE FROM groups_users WHERE user_id = ? AND group_id = ?";
            $query = $this->con->prepare($q);
            $query->execute(array($user_id, $this->id));
            if($query->rowCount() === 1) return true;
        }
        return false;
    }


}