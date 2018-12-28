<?php
namespace Pickle\Tools;

class Config{

    static $host = "localhost";//host name
    static $username = "root";//username
    static $password = "";//password
    static $database = "pickle";//name of the database

    static $CacheDirectory = "/temp";//the directory where the cache will be stored

    static $env = 'DEV';//DEV or PROD

    static $devmail = null;

}