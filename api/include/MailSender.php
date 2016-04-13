<?php

require_once 'LoggerHandler.php';
require_once 'swiftmailer-5.x/lib/swift_required.php';

/*
$m = new MailSender();
$m->sendMail($data);
*/

class MailSender {
    public $mail;
    public $plaintext;
    
    const SMTP_TRANSPORT = "smtp.mandrillapp.com";
    const SMTP_PORT = 587;
    const SMTP_USERNAME = "info@transportesejecutivos.com";
    const SMTP_PASSWORD = "DiP2MT9BtAmZs67cQG0alA";
    
    public function __construct() {
        
    }
    
    public function setMail($data) {
        $this->mail = $data->mail;
        $this->plaintext = $data->plaintext;
    }

    public function sendMail($data) {
        $log = new LoggerHandler();
        $log->writeString("Iniciando proceso");
        
        try {
            $transport = Swift_SmtpTransport::newInstance(MailSender::SMTP_TRANSPORT, MailSender::SMTP_PORT)
                ->setUsername(MailSender::SMTP_USERNAME)
                ->setPassword(MailSender::SMTP_PASSWORD);

            $mailer = Swift_Mailer::newInstance($transport);

            // Create the message
            $message = Swift_Message::newInstance()
                // Give the message a subject
                ->setSubject('Este es mi asunto')

                // Set the From address with an associative array
                ->setFrom(array('info@transportesejecutivos.com' => 'Transportes Ejecutivos'))

                // Set the To addresses with an associative array
                ->setTo(array('will.montiel@aol.com', 'willtechandscience@gmail.com' => 'Will Montiel'))

                // Give it a body
                ->setBody($this->mail, 'text/html')

                // And optionally an alternative body
                ->addPart($this->plaintext, 'text/plain');

            $result = $mailer->send($message);
        } 
        catch (Exception $ex) {
            $log->writeString("Exception: " . $ex->getMessage());
            $log->writeString("Exception: " . $ex->getTraceAsString());
        }
    }  
}