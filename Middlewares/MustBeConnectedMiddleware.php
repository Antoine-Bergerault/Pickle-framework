<?php

use Pickle\Engine\App;
use Pickle\Engine\Router;

class MustBeConnectedMiddleware {

    public function __construct(){
        Router::condition(Router::$routes[App::getMethod()], App::is_connected());
        if(!App::is_connected()) {
            Router::get('/login', ['view' => 'user/login', 'name' => 'login']);
            Router::post('/login', 'UserController@login');
            Router::default_path('/login');
        }else{
            Router::get('/logout',function (){
                App::logout();
                redirect(route('login'));
            });
        }
    }

}