<?php
namespace Pickle\Engine;

class Email{

    static $name = "Basic mvc system";
    static $email = "noreply@youremail.fr";

    static function send($subject,$message,$mailto,$from = null){
        if($from == null){
            $from = self::$email;
        }
        $header = self::generate_header($from);
        try{
            $mail = mail($mailto,$subject,$message,$header);
        }catch(\Exception $e){
            $mail = false;
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