<?php
/** @var string $url */

define('ROOT', dirname(__DIR__));

use \Pickle\Engine\App;
use \Pickle\Engine\Router;

ob_start('ob_gzhandler');
register_shutdown_function('ob_end_flush');

$url = '/';
if(isset($_GET['url'])){//set the url
    $url = '/'.$_GET['url'];
}

date_default_timezone_set('UTC');

require('../autoload.php');

//App::activeMiddlewares(['mustBeConnected']);

echo Router::run($url);

App::extra();

App::getEnvironnementTools();