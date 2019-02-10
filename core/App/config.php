<?php

class AppConfig{

    static $uses = [
        /**
        Class(es)
        */
        'Session' => 'default/Session',

        /**
         * Trait(s)
         */
        'UserManagement' => 'default/UserManagement'
    ];

    static function load(){

        foreach(self::$uses as $class => $file){
            require "Components/$file.php";
        }

    }

}

?>