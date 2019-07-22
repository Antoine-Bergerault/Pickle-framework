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
        'UserManagement' => 'default/UserManagement',
        'SessionChecker' => 'SessionChecker',
        'StyleManagement' => 'StyleManagement'
    ];

    static $cacheclass = 'default/Cache';
    
    static function load(){

        foreach(self::$uses as $class => $file){
            require "Components/$file.php";
        }

    }

    static function cache(){
        $c = self::$cacheclass;
        require "Components/$c.php";
    }

}

?>