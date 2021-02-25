<?php


namespace App\Controllers;


use App\Config;
use App\Mails\WelcomeMail;
use App\Models\Group;
use App\Models\User;
use Core\Controller;
use Core\Mailer;
use Core\View;
use App\Models\Auth;
use PHPMailer\PHPMailer\PHPMailer;


class Groups extends Controller
{

    /**
     * Renders Create Group Form page
     *
     * @throws \Exception
     */
    public function createAction(){
        $group = new Group();

        View::render('Groups/create.php', ['countries'=>$group->countries()]);
    }

    /**
     * Prints json_encoded @array of cities from the country received by GET request
     *
     * Used for AJAX request
     * @return void
     */
    public function cities(){
        $country = filter_input(INPUT_GET, 'country', FILTER_SANITIZE_STRING);
        $group = new Group();
        print json_encode($group->countryCities($country));
    }

    /**
     * Create new group
     *
     * Receive 'group_name', 'city', 'country' and 'about' strings by POST request
     *
     * On finish, redirects with session message and response code
     */
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

        if($id = $group->create($name, $country, $city, $about)){
            $_SESSION['message'] = "Group created";
            header("Location: /groups/$id/show"); exit(200);
        }else{
            $_SESSION['error'][] = "Unknown error. Group not created.";
            header("Location: /groups/create"); exit(500);
        }

    }

    /**
     * Shows specific group by provided group id as a parameter in the route
     *
     * Render view or @throws \Exception
     */
    public function showAction(){

        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $group = new Group();
        $g = $group->find($id);
        if($g){
            View::render('/Groups/show.php', ['group'=>$g]);
        } else{
            throw new \Exception("Group not found", 404);
        }
    }


    public function editAction(){
        $group_id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        //check if owns this group
        $group = new Group();
        if($group->isOwner($group_id)){
            $group = $group->find($group_id);

            View::render('/Groups/create.php', ['group'=> $group, 'countries'=>$group->countries()]);
        }else{
            throw new \Exception("Not Authorized", 403);
        }

    }


    public function update(){
        $_SESSION['error'] = array();

        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);

        if($_POST['token'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Wrong parameters sent. Group not updated.";
            header("Location: /groups/$id/edit");
            exit(500);
        }

        $group = new Group();
        if(!$group->isOwner($id)){
            throw new \Exception("Not Authorized", 403);
        }

        $name = trim(filter_input(INPUT_POST, 'group_name', FILTER_SANITIZE_STRING));


        if(strlen($name) < 1) $_SESSION['error']['group_name'] = "Group name should be at least 1 character long";


        $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING));
        if(strlen($city) < 2)  $_SESSION['error']['city'] = "City name should be at least 2 characters long";

        $country = trim(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING));
        if(!in_array($country, $group->countries())) $_SESSION['error']['country'] = "Country name not recognized. Please choose country from the list";

        $about = filter_input(INPUT_POST, 'about', FILTER_SANITIZE_STRING);

        if(count($_SESSION['error']) > 0){
            header("Location: /groups/$id/edit");
            exit(403);
        }

        if($group->update($id, $name, $country, $city, $about)){
            $_SESSION['message'] = "Group updated";
            $rcode = http_response_code(200);
        }else{
            $_SESSION['error'][] = "Unknown error. Group not updated.";
            $rcode = http_response_code(500);
        }
        header("Location: /groups/$id/show");
        exit($rcode);
    }


    public function destroyAction(){
        $id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);

        if($_POST['token'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Wrong parameters sent. Group not deleted.";
            header("Location: /groups/$id/show");
            exit(500);
        }
        $group = new Group();

        if(!$group->isOwner($id)){
            throw new \Exception("Not Authorized", 403);
        }

        if($group->delete($id)){
            $_SESSION['message'] = "Group deleted";
            header("Location: /"); exit(200);
        }else{
            $_SESSION['error'][] = "Unknown error. Group not deleted.";
            header("Location: /groups/$id/show"); exit(500);
        }
    }

    /* MEMBERS */

    public function inviteAction(){
        $group_id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);

        if($_GET['token'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Wrong parameters sent. User not invited.";
            header("Location: /groups/$group_id/show");
            exit(500);
        }
        $group = new Group();
        $myGroup = $group->find($group_id);

        //Check if auth user is owner of this group
        if($group->owner_id !== Auth::id()) {
            throw new \Exception( "Access Denied", 403);
        }

        //Check if there are no more than 20 members invited
        if(count($group->invitedMembers()) > 20){
            $_SESSION['error'][] = "To many invitations pending. Wait till users accept sent invitations for this group.";
            header("Location: /groups/$group_id/show");
            exit(403);
        }
        $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
        $user = new User();
        $users = $user->where('email', $email);

        if(empty($users)){
            //create new user
            $password = bin2hex(random_bytes(8));
            $user_id = $user->store(['name'=>$name, 'email'=>$email, 'password'=>$password]);
            $user = $user->find($user_id);
        }else{
            $user = $users[0];

            //check if user is not already in this group
            if($group->checkIfUserIsInGroup($user->id, $myGroup->id)){
                $_SESSION['error'][] = "User is already invited in this group.";
                header("Location: /groups/$group_id/show");
                exit(403);
            }

        }
        unset($user->password);//user password should not be in the instance

        $hash = bin2hex(random_bytes(30));

        //create record in groups_users table and save hash
        if(!$group->makeInviteHash($myGroup->id, $user->id, $hash)){
            $_SESSION['error'][]= "User not saved in the group";
            header("Location: /groups/$group_id/show");
            exit(500);
        }

        //create the email message
        $url = Config::getConfig('app.url') . "/groups/$myGroup->id/acceptInvitation?hash=$hash";
        $subject = 'Invitation to MyDictionary ' . $myGroup->name . " group from " . $myGroup->owner()->name;
        $content = 'Dear ' . $user->name . ',<br><br> <strong>You are invited as a member
         to our group ' . $myGroup->name . ' at MyDictionary application.</strong> <br>Please click on the following link 
         to accept this invitation: <a href="' . $url . '">'. $url .'</a>';

        //If it is a newly created user...
        if(isset($password)) {
            $content .= "<br><br>You can log in to the <a href='" . Config::getConfig('app.url') . "'>" .
            Config::getConfig('app.name') . "</a> with your email as username and the password: 
            <strong>" . $password . "</strong>";
        }

        $m = new WelcomeMail();
        $message = $m->setMessage($subject, $content);

        //use Mailer class to send emails
        $mail = new Mailer();
        $mail->setMail($user, $subject, $message);
        if($mail->send()){
            $_SESSION['message'] = "Message sent";
        }else{
            $_SESSION['error'][]= 'There was some error. Message could not be sent.';
        }

        header("Location: /groups/$myGroup->id/show");
        exit();

    }


    public function acceptInvitationAction(){
        $received_hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);
        $group = new Group();
        $result = $group->clearInviteHash($received_hash);
        if($result){
            $group = $group->find($result->group_id);
            $user = new User();
            $user = $user->find($result->user_id);

            //send welcome mail

            $subject = "Welcome to ". $group->name;
            $content = "Dear ". $user->name . ",<br><br>";
            $content .= "I am " . $group->owner()->name . ", moderator of the group " . $group->name . "<br>";
            $content .= "Welcome to our group at the MyDictionary.<br>";
            $content .= "Here we share our words/phrases that we insert into the dictionary<br>";
            $content .= "during our language study adventure. We hope you will have fun and find<br>";
            $content .= "this membership in the group useful to your learning process.";

            $m = new WelcomeMail();
            $message = $m->setMessage($subject, $content);
            $mail = new Mailer();
            $mail->setMail($user, $subject, $message);
            $mail->send();
        }
        header("Location: /");
        exit();
    }

    public function destroyMemberAction(){

        $group_id = filter_var($this->route_params['id'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);
        $user_id = filter_var($this->route_params['aid'], FILTER_SANITIZE_NUMBER_INT, ['min'=>1]);

        if($_POST['token'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Wrong parameters sent. User not removed.";
            header("Location: /groups/$group_id/show");
            exit(500);
        }

        $group = new Group();
        $group = $group->find($group_id);
        if($group){
            if($group->removeUser($user_id)){
                $_SESSION['message'] = "Member removed from the group";
                header("Location: /groups/$group_id/show");
                exit(200);
            }
        }
        $_SESSION['error'][]= "There was some error. User not removed. Please try again later.";
        header("Location: /groups/$group_id/show");
        exit(500);
    }
}