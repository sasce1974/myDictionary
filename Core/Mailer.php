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
        $this->mail->Host = Config::getConfig('app.smtp.host');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = Config::getConfig('app.smtp.user');
        $this->mail->Password = Config::getConfig('app.smtp.pass');
        $this->mail->SMTPSecure = Config::getConfig('app.smtp.secure');
        $this->mail->Port = Config::getConfig('app.smtp.port');
    }


    public function setMail($user, $subject, $message, $from=null){
        if($from==null) $from = Config::getConfig('app.smtp.from');
        $this->mail->setFrom($from, Config::getConfig('app.name'));
        $this->mail->addAddress($user->email, $user->name);
        //todo method addAddress should be used directly from the script that sends the email,
        // since it can be used multiple times for sending emails to more addresses!
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
            //$_SESSION['error'][]= 'There was some error. Message could not be sent.';
            error_log('Message could not be sent. Error: ' . $this->mail->ErrorInfo);
            return false;
        } else {
            //$_SESSION['message'] = "Message sent";
            return true;
        }
    }

}
