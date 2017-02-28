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
    const SMTP_USERNAME = "info@transportesejecutivos.com";
    const SMTP_PASSWORD = "ZoQvWRw8tAsb3VsIBje_dQ";

    public function __construct() {
        
    }

    public function setMail($data) {
        $this->html = $data->html;
        $this->plaintext = $data->plaintext;
    }

    public function sendMail($data) {
        try {
            $log = new LoggerHandler();
            $log->writeString("Username: " . MailSender::SMTP_USERNAME);
            $log->writeString("Password: " . MailSender::SMTP_PASSWORD);
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

            if (!$mailer->send($message)) {
                $log->writeString("Se finalizó el servicio, pero no se pudo enviar el resumen por correo al cliente");
//        throw new InvalidArgumentException("Se finalizó el servicio, pero no se pudo enviar el resumen por correo al cliente");
            }
        } catch (Exception $ex) {
            $log = new LoggerHandler();
            $log->writeString("Exception while sending email with mandrill and swiftmailer: " . $ex->getMessage());
            $log->writeString("Exception while sending email with mandrill and swiftmailer: " . $ex->getTraceAsString());
            throw new Exception($ex->getMessage());
        }
    }

}
