<?php

interface SessionInterface {

    /**
     * start a session if it's not already done
     */    
    static function session();

    static function clear_session();

    /**
     * @param key the key of the session
     */
    static function destroy($key);

    /**
     * @param key the key of the session
     * @param val the value to assign to the session key
     */
    static function save($key, $val = true);

    static function add($key, $val);

    /**
     * @param key the key of the session
     */
    static function isset_session($key);

    static function get($key);

}



?>