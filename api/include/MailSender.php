<?php

require_once 'LoggerHandler.php';
require_once 'swiftmailer-5.x/lib/swift_required.php';

/*
$m = new MailSender();
$m->sendMail($data);
*/

class MailSender {
    public $html;
    public $plaintext;
    
    const SMTP_TRANSPORT = "smtp.mandrillapp.com";
    const SMTP_PORT = 587;
    const SMTP_USERNAME = "info@zonaenlinea.com";
    const SMTP_PASSWORD = "xI9JscYclOD4QJ64vdbFRQ";
    
    public function __construct() {
        
    }
    
    public function setMail($data) {
        $this->html = $data->html;
        $this->plaintext = $data->plaintext;
    }

    public function sendMail($data) {
        $log = new LoggerHandler();
        $log->writeString("Enviando Resumen con los siguientes datos");
        $log->writeArray(print_r($data, true));
        
        try {
            $transport = Swift_SmtpTransport::newInstance(MailSender::SMTP_TRANSPORT, MailSender::SMTP_PORT)
                ->setUsername(MailSender::SMTP_USERNAME)
                ->setPassword(MailSender::SMTP_PASSWORD);

            $mailer = Swift_Mailer::newInstance($transport);

            // Create the message
            $message = Swift_Message::newInstance()
                // Give the message a subject
                ->setSubject($data->subject)

                // Set the From address with an associative array
                ->setFrom($data->from)

                // Set the To addresses with an associative array
                ->setTo($data->to)

                // Give it a body
                ->setBody($this->html, 'text/html')

                // And optionally an alternative body
                ->addPart($this->plaintext, 'text/plain');

            if(!$mailer->send($message)) {
                throw new InvalidArgumentException("Se finalizó el servicio, pero no se pudo enviar el resumen por correo al cliente");
            }
        } 
        catch (Exception $ex) {
            $log->writeString("Exception: " . $ex->getMessage());
            $log->writeString("Exception: " . $ex->getTraceAsString());
            throw new Exception($ex->getMessage());
        }
    }  
}