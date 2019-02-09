<?php
namespace Pickle\Engine;
include ROOT.'/core/App/Interfaces/SessionInterface.php';

class Session implements \SessionInterface{

    static function session(){//start a session if it is not already done

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION;

    }

    static function clear_session(){
        $_SESSION = [];
    }

    /**
     * @param key the key of the session
     */
    static function destroy($key){
        self::session();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * @param key the key of the session
     * @param val the value to assign to the session key
     */
    static function save($key, $val = true){
        self::session();
        $_SESSION[$key] = $val;
        return true;
    }

    /**
     * @param key the key of the session
     * @param val the value to assign to the session key
     */
    static function add($key, $val){
        self::session();
        if (!self::isset_session($key)) {
            self::save($key, []);
        }
        if (!is_array(self::get($key))) {
            self::save($key, [self::get($key)]);;
        }
        $arr = self::get($key);
        $arr[] = $val;
        self::save($key, $arr);
    }

    /**
     * @param key the key of the session
     */
    static function isset_session($key){
        return isset($_SESSION[$key]);
    }


    static function get($key){
        self::session();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return false;
    }


}


?>