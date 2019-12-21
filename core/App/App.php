<?php
namespace Pickle\Engine;
use Pickle\Tools\Config;

require 'config.php';
\AppConfig::load();

class App extends Session{

    use UserManagement;
    use SessionChecker;
    use StyleManagement;

    static $user = false;//variable to store the user
    static $url = null;//variable to store the url
    static $extras = [];//extras
    static $middlewares = [];
    static $modules = [];
    static $components = true;

    static function load(){//equivalent of __construct
        self::session();//create a session if doesn't exist
        if(isset($_SESSION) && isset($_SESSION['user'])){
            $usr = $_SESSION['user'];//set the variable with the session content
            self::connect($usr);//connect to the user
        }
        if(self::is_connected()){
            if(self::get('ip') != $_SERVER['REMOTE_ADDR']){
                self::logout();
                self::destroy('ip');
            }
        }
        if(isset($_COOKIE['user']) && !self::is_connected()){
            self::fromcookie();
        }
        self::seturl();//initialize the $url
        
        $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;
        define('ENV', ($header === 'XMLHttpRequest') ? null : Config::$env);
        if ($header === 'XMLHttpRequest'){
            self::disableComponents();
        }

        self::load_extra();
        self::autoload();
    }

    static function load_extra(){
        if (self::$components == true) {
            require_once(__DIR__.'/Extras.php');
            $Extras = new Extras();

            if(self::isset_session('flash')){
                echo $Extras->flashes(self::get('flash'));
                self::destroy('flash');
            }
        }
    }

    static function extra(){
        if (self::$components == true) {
            require_once(__DIR__.'/Extras.php');
            $Extras = new Extras();
            if(self::isset_session('debug')){
                echo $Extras->debug(self::get('debug'));
            }
        }
    }

    static function back(){//a shortcut to go to the prevent page

        echo '<script>history.back()</script>';

    }

    static function seturl($nurl = null){//set the url

        if($nurl != null){
            self::$url = $nurl;
        }else{
            self::$url = (isset($_SERVER['HTTPS']) ? "https://" : "http://"). "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

    }

    static function debug($v){
        self::save('debug', debug($v));
    }

    /**
     * @param $name
     * @return mixed
     */
    static function middleware($name){
        if(!isset(self::$middlewares[$name])){
            require_once(ROOT.'/src/Middlewares/'.ucfirst($name).'Middleware.php');
            $name = ucfirst($name).'Middleware';
            $middleware = new $name();
            self::$middlewares[$name] = $middleware;
            return $middleware;
        }else{
            return self::$middlewares[$name];
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    static function module($name){
        if (!isset(self::$modules[$name])) {
            require_once(__DIR__ . '/../Modules/' . ucfirst($name) . 'Module.php');
            $name = ucfirst($name) . 'Module';
            $module = new $name();
            self::$modules[$name] = $module;
            return $module;
        } else {
            return self::$modules[$name];
        }
    }

    static function activeMiddlewares($arr){
        if(!is_array($arr)){
            $arr = [$arr];
        }
        foreach($arr as $m){
            self::middleware($m);
        }
    }

    static function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    static function autoload(){
        function endWith($name, $test){
            $length = strlen($test);
            if($length === 0){
                return true;
            }
            return substr($name, -$length) === $test;
        }
        spl_autoload_register(function($name){
            $arr = explode('\\', $name);
            if($arr[0] == 'Pickle'){
                $name = str_replace('Pickle\\', '', $name);
            }
            if(endWith($name, 'Model')){
                require_once ROOT."/src/Models/$name.php";
            }elseif (endWith($name, 'Controller')){
                require_once ROOT."/src/Controllers/$name.php";
            }
        });
    }

    static function getEnvironnementTools($env = null){
        if(self::$components == true){
            if($env == null){
                $env = ENV;
            }
            include __DIR__.'/Builders/AppBuilder.php';
            $Builder = new \Pickle\Engine\Builder\AppBuilder();
            $Builder->loadEnvironnement($env);
            $Builder->print();
        }
    }

    static function disableComponents(){
        self::$components = false;
    }

}

App::load();

?>