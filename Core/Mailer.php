<?php


namespace Core;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer();

        $this->mail->isSMTP();
        $this->mail->Host = Config::getConfig('app.smtp.host'); //'smtp.mailtrap.io';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = Config::getConfig('app.smtp.user'); //'97979891200e02';
        $this->mail->Password = Config::getConfig('app.smtp.pass'); //'6336cac52d41bf';
        $this->mail->SMTPSecure = Config::getConfig('app.smtp.secure'); //null;
        $this->mail->Port = Config::getConfig('app.smtp.port'); //465;
    }


    public function setMail($user, $subject, $message, $from=null){
        if($from==null) $from = Config::getConfig('app.smtp.from'); //'admin@dictionary.papucraft.com';
        $this->mail->setFrom($from, Config::getConfig('app.name'));
        $this->mail->addAddress($user->email, $user->name);
        $this->mail->Subject = $subject;

        // Set HTML
        $this->mail->isHTML(TRUE);
        $this->mail->Body = $message;
        $this->mail->AltBody = 'Hi there.';
    }

    public function addAttachment($file_path, $file_name){
        $this->mail->addAttachment($file_path, $file_name);
    }


    public function send(){
        // send the message
        if(!$this->mail->send()){
            $_SESSION['error'][]= 'Message could not be sent. Error: ' . $this->mail->ErrorInfo;
            error_log('Message could not be sent. Error: ' . $this->mail->ErrorInfo);
            return false;
        } else {
            $_SESSION['message'] = "Invitation sent";
            return true;
        }
    }

}