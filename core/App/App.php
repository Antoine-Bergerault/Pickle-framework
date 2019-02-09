<?php
namespace Pickle\Engine;
use Pickle\Tools\Config;

require 'config.php';
\AppConfig::load();

class App extends Session{

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

    static function connect($usr, $remind = false){//connect the app to the user passed in parameter as $usr
        self::$user = $usr;
        self::session();
        $_SESSION['user'] = self::$user;//store the user in session
        if($remind){
            self::saveusercookie($usr);
        }
        self::save('ip', $_SERVER['REMOTE_ADDR']);
    }

    static function saveusercookie($usr){
        setcookie('user', self::cookieinfo($usr), time() + 3600 * 24, '/', 'wallp.local', false, true);
    }

    static function cookieinfo($user){
        return $user->id .'*'. sha1($user->name . $user->pass . $_SERVER['REMOTE_ADDR']);
    }

    static function fromcookie(){
        if(isset($_COOKIE['user'])){
            $cookie = $_COOKIE['user'];
            $cookie = explode('*',$cookie);
            $usr = self::getuser($cookie[0]);
            if($usr == false){
                return false;
            }
            if(self::cookieinfo($usr) == implode('*', $cookie)){
                self::connect($usr);
                self::saveusercookie($usr);
            }
        }
        return false;
    }

    static function getuser($id){
        require __DIR__.'/../Models/UserModel.php';
        $model = new User();
        return $model->user_info([
            'id' => $id
        ]);
    }

    static function is_connected(){//return true if the user exist, else false

        if(self::$user != false){

            return true;

        }

        return false;

    }

    static function getname(){//return the name of the user and false if we are not connected

        if(self::is_connected()){

            $usr = self::$user;
            return $usr->name;

        }

        return false;

    }

    static function getid(){//return the id of the user and false if we are not connected

        if(self::is_connected()){

            $usr = self::$user;
            return $usr->id;

        }

        return false;

    }

    static function back(){//a shortcut to go to the prevent page

        echo '<script>history.back()</script>';

    }

    static function logout(){//logout the current user if we are connected

        self::$user = false;
        if(isset($_SESSION) && isset($_SESSION['user'])){
            $_SESSION['user'] = false;
        }
        self::clear_session();

    }

    static function seturl($nurl = null){//set the url

        if($nurl != null){
            self::$url = $nurl;
        }else{
            self::$url = (isset($_SERVER['HTTPS']) ? "https://" : "http://"). "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }

    }
    
    static function is_role($role_name){
        if(self::is_connected()){
            if(isset(self::$user->group_name) && in_array($role_name, self::$user->group_name)){
                return true;
            }
            return false;
        }
        return false;
    }

    static function is_available($action_name){
        if(self::is_connected()){
            $actions = self::$user->actions;
            if(in_array($action_name, $actions)){
                return true;
            }
            return false;
        }
        return false;
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
            require_once(__DIR__.'/../Middlewares/'.ucfirst($name).'Middleware.php');
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
                require_once __DIR__."/../Models/$name.php";
            }elseif (endWith($name, 'Controller')){
                require_once __DIR__."/../Controllers/$name.php";
            }
        });
    }

    static function getEnvironnementTools($env = null){
        if(self::$components == true){
            if($env == null){
                $env = ENV;
            }
            include __DIR__.'/Builders/AppBuilder.php';
            if($env == 'DEV'){
                $Builder = new \Pickle\Engine\Builder\AppBuilder();
                $Builder->loadEnvironnement($env);
                $Builder->print();
            }
        }
    }

    static function disableComponents(){
        self::$components = false;
    }

}

App::load();

?>