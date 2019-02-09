<?php

class AppConfig{

    static $uses = [
        'Session' => 'default/Session'
    ];

    static function load(){

        foreach(self::$uses as $class => $file){
            require "Components/$file.php";
        }

    }

}

?>