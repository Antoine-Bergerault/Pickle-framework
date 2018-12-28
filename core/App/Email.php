<?php
namespace Pickle\Engine;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

class Email{

    static $name = "Pickle framework";
    static $email = "noreply@youremail.fr";

    static function send($subject,$message,$mailto = null,$from = null){
        if($mailto == null){
            return false;
        }
        $mail = new PHPMailer(true);
        //$message = utf8_decode($message);
        $subject = utf8_decode($subject);
        if ($from == null) {
            $from = self::$email;
        }
        $header = self::generate_header($from);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Host = 'your.host';           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'username';                 // SMTP username
            $mail->Password = 'pass';           // SMTP password
            $mail->Port = 587;
    //Recipients
            $mail->setFrom(self::$email, self::$name);
            $mail->addAddress($mailto);              // Name is optional

    //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = self::generate_content($message, $ad);
            debug($mail);
            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

        return $mail;
    }

    static function generate_header($from){
        
        date_default_timezone_set('UTC');

        $header = 'MIME-Version: 1.0'.PHP_EOL
        .'From: '.self::$name.'<'.$from.'>'.PHP_EOL
        .'Return-Path: '.$from.PHP_EOL
        .'Reply-To: '.$from.PHP_EOL
        .'Organization: '.self::$name.PHP_EOL 
        .'X-Priority: 3 (Normal)'.PHP_EOL 
        .'Content-Type: text/html; charset="iso-8859-1"'.PHP_EOL
        .'Content-Transfer-Encoding: 8bit'.PHP_EOL
        .'X-Mailer: PHP '.PHP_EOL
        .'Date:'. date("r") . PHP_EOL;

        return $header;

    }

}


?>