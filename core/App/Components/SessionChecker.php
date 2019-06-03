<?php
namespace Pickle\Engine;
use Pickle\Tools\Config;
use Pickle\Engine\SessionChecker\Module;
use Pickle\Engine\SessionChecker\Reader;

require_once 'SessionChecker/Module.php';
require_once 'SessionChecker/Reader.php';

trait SessionChecker{

    static function checkSession(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $Module = new Module();
        $Reader = new Reader();

        $data = $Reader->read();
        $Module->act($data);

    }

    static function addSessionCheckdata(String $name, \Pickle\Engine\SessionChecker\SessionCommand $data){
        $Reader = new Reader();
        $obj = new \stdClass();
        $obj->content = $data->reduce();
        $obj->app = ['id' => mt_rand(10000, 99999)];
        file_put_contents($Reader->root . '/' . $name, serialize($obj));
    }
    
    static function getSessionCommand($action){
        require_once 'SessionChecker/SessionCommand.php';
        return new \Pickle\Engine\SessionChecker\SessionCommand($action);
    }

}


?>