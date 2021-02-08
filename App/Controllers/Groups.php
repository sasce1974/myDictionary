<?php


namespace App\Controllers;


use App\Models\Group;
use Core\Controller;
use Core\View;

class Groups extends Controller
{

    public function createAction(){
        $group = new Group();

        View::render('Groups/create.php', ['countries'=>$group->countries()]);
    }

    public function cities(){
        $country = filter_input(INPUT_GET, 'country', FILTER_SANITIZE_STRING);
        $group = new Group();
        print json_encode($group->countryCities($country));
    }

    public function store(){
        $_SESSION['error'] = array();

        $name = trim(filter_input(INPUT_POST, 'group_name', FILTER_SANITIZE_STRING));


        if(strlen($name) < 1) $_SESSION['error']['group_name'] = "Group name should be at least 1 character long";

        $group = new Group();
        if($group->nameExist($name)) $_SESSION['error']['group_name'] = "Group with name $name already exist. Please choose another group name";

        $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING));
        if(strlen($city) < 2)  $_SESSION['error']['city'] = "City name should be at least 2 characters long";

        $country = trim(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING));
        if(!in_array($country, $group->countries())) $_SESSION['error']['country'] = "Country name not recognized. Please choose country from the list";

        $about = filter_input(INPUT_POST, 'about', FILTER_SANITIZE_STRING);

        if(count($_SESSION['error']) > 0){
            header("Location: /groups/create?group_name=$name&country=$country&city=$city&about=$about");
            exit(403);
        }

        if($id = $group->save($name, $country, $city, $about)){
            $_SESSION['success'] = "Group created";
            header("Location: /groups/$id/show"); exit(200);
        }else{
            $_SESSION['error'][] = "Unknown error. Group not created.";
            header("Location: /groups/create"); exit(500);
        }

    }

    public function showAction(){

        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $group = new Group();
        $g = $group->find($id);
        View::render('/Groups/show.php', ['group'=>$group]);
    }
}