<?php
namespace Pickle\Tools;

class Config{

    static $host = null;//host name
    static $username = null;//username
    static $password = null;//password
    static $database = null;//name of the database

    static $CacheDirectory = "/temp";//the directory where the cache will be stored

    static $env = 'DEV';//DEV or PROD or RESTRCITED

    static $devmail = null;

    static $website = 'localhost/pickle';

}

Config::$host = $GLOBALS['settings']['host'] ?? 'localhost';
Config::$username = $GLOBALS['settings']['username'] ?? 'root';
Config::$password = $GLOBALS['settings']['password'] ?? '';
Config::$database = $GLOBALS['settings']['database'] ?? 'pickle';

Config::$website = $GLOBALS['settings']['site'] ?? 'localhost/pickle';