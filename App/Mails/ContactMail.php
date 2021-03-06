<?php


namespace App\Mails;


use App\Config;

class ContactMail
{
    private $message;

    public function setMessage($subject, $content){
        $this->message = "<html><head><title>$subject</title>";
        $this->message .= "<style type='text-css'>body{font-family: 'Spartan', sans-serif, Arial}</style>";
        $this->message .= "</head><body><div style='text-align: center'>\r\n";
        $this->message .= "<img style='vertical-align: middle;margin:auto;' src='https://dictionary.papucraft.com/images/logo_dictionary1.svg' alt='logo' width=80 height='auto'>\r\n";
        $this->message .= "<h3 style='color: darkblue;padding:1em;vertical-align: middle;border-radius: 6px'>$subject</h3></div><br>\r\n";
        $this->message .= "<div style='text-align: left'>\r\n";
        $this->message .= $content;

        $this->message .= "<br><br>Best regards,<br>" . Config::getConfig('app.name');
        $this->message .= "<br></div></body></html>";

        return $this->message;
    }
}