<?php


namespace App\Controllers;


use App\Config;
use App\Mails\ContactMail;
use App\Models\User;
use Core\Controller;
use Core\Mailer;
use Core\View;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;

class Contact extends Controller
{
    public function indexAction(){
        View::render('/regorlog.php');
    }

    public function messageAction(){
        // message that will be displayed when everything is OK :)
        $okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';

        // If something goes wrong, we will display this message.
        $errorMessage = null;

        if (!isset($_POST['g-recaptcha-response'])) {
            $errorMessage = 'ReCaptcha is not set.';
        }

        $secret_key = Config::getConfig('app.captcha.secret_key');

        $recaptcha = new ReCaptcha($secret_key, new CurlPost());

        $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$response->isSuccess()) {
            $errorMessage = 'ReCaptcha was not validated.';
        }

        $required= array("email", "name", "message");
        foreach ($required as $requiredField) {
            if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
                $errorMessage = $requiredField . " required.";
            }
        }
        //check if submitted token is valid...
        if(!isset($_POST['init']) || $_POST['init'] !== $_SESSION['token']){
            $errorMessage = "Error in the submitted data, please try again.";
            unset($_SESSION['token']);
        }

        if (!preg_match("/^[a-zA-Z .]+$/", $_POST["name"])) {
            $errorMessage = "Name and surname can contain only letters.";
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Invalid email address"; }

        if (strlen(trim($_POST["message"])) < 10) {
            $errorMessage = "Message is too short.";
        }
        $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
        $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

        if($errorMessage === null){
            $subject = "New message from contact form on MyDictionary";

            $content = "You have a message from: <br>";
            $content .= "Name: <b>$name</b> <br>Email: <b>$email</b> <br>";
            $content .= "With the following content: <br><br>";
            $content .= "<em>" . $message . "</em>";

            //send message
            $m = new ContactMail();
            $message = $m->setMessage($subject, $content);

            //use Mailer class to send emails
            $mail = new Mailer();
            $user = new User();
            $user->email = 'admin@dictionary.papucraft.com';
            $user->name = 'MyDictionary';
            $mail->setMail($user, $subject, $message);
            $mail->send();

            $responseArray = array('type' => 'success', 'message' => $okMessage);
        }else{
            //$errorMessage = 'There was an error while submitting the form. Please try again later';
            $responseArray = array('type' => 'danger', 'message' => $errorMessage);
        }

        // if requested by AJAX request return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
        }
        // else just display the message
        else {
            echo $responseArray['message'];
        }


    }


    public function odl_messageAction(){
        $_SESSION['error'] = array();
        $errorMessage = "";
        $okMessage = "";

        $required= array("email", "name", "message");
        foreach ($required as $requiredField) {
            if (!isset($_POST[$requiredField]) || $_POST[$requiredField] == "") {
                $_SESSION["error"][] = $requiredField . " required.";
            }
        }
        //check if submitted token is valid...
        if(!isset($_POST['init']) || $_POST['init'] !== $_SESSION['token']){
            $_SESSION['error'][] = "Error in the submitted data, please try again.";
            unset($_SESSION['token']);
        }

        if (!preg_match("/^[a-zA-Z .]+$/", $_POST["name"])) {
            $_SESSION["error"][] = "Name and surname can contain only letters.";
        }

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "Invalid email address"; }

        if (strlen(trim($_POST["message"])) < 10) {
            $_SESSION["error"][] = "Message too short.";
        }
        $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));
        $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

        $url = urlencode("?email={$email}&name={$name}&message={$message}");

        if (count($_SESSION["error"]) > 0) {
            header("Location: /contact/" . $url);
            exit(400);

        } else {
            $subject = "New message from contact form on MyDictionary";

            $content = "You have a message from: <br>";
            $content .= "Name: <b>$name</b> <br>Email: <b>$email</b> <br>";
            $content .= "With the following content: <br><br>";
            $content .= "<em>" . $message . "</em>";

            //send message
            $m = new ContactMail();
            $message = $m->setMessage($subject, $content);

            //use Mailer class to send emails
            $mail = new Mailer();
            $user = new User();
            $user->email = 'admin@dictionary.papucraft.com';
            $user->name = 'MyDictionary';
            $mail->setMail($user, $subject, $message);
            $mail->send();
        }
        header("Location: /contact");
        exit();
    }
}