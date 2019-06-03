<?php
use Pickle\Engine\Router;
use Pickle\Engine\App;

Router::get('/home', [ 'view' => 'home', 'script' => 'MainController@home', 'reload' => 15000, 'cache' => false, 'data' => [
    'title' => 'Homepage'
]]);

Router::get('/template', ['view' => 'template', 'amp' => false]);

Router::default_path('/home');
Router::setAjax('/load');
Router::setAmp('/amp');

?>