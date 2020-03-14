<?php
/** @var string $url */

define('ROOT', dirname(__DIR__));

use Yalesov\Yaml\Yaml;
use \Pickle\Engine\App;
use \Pickle\Engine\Router;

ob_start('ob_gzhandler');
register_shutdown_function('ob_end_flush');

$url = '/';
if(isset($_GET['picklerewriteurl'])){//set the url
    $url = '/'.$_GET['picklerewriteurl'];
}

date_default_timezone_set('UTC');

require(ROOT.'/autoload.php');

require ROOT.'/vendor/autoload.php';

$settings = Yaml::parse(file_get_contents(ROOT.'/environment.yml'));

$GLOBALS['settings'] = $settings;

if (($_SERVER["HTTPS"] ?? 'off') != "on") {
    header("HTTP/1.1 301 Moved Permanently");
    $site = $settings['site'];
    header("Location: https://$site$url");
    exit();
}

if(ENV == 'PROD'){
    register_shutdown_function('fatal_handler');
    function fatal_handler(){
        $errfile = "unknown file";
        $errstr = "shutdown";
        $errno = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if ($error !== null) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];

            $subject = 'New error';
            $message = 'This error occurs when trying to load the page : ' . url($url) . '<br>';
            $message .= "$errno : $errstr in <strong>$errfile</strong> on line <strong>$errline</strong>";

            require ROOT . '/core/App/Email.php';
            Pickle\Engine\Email::send($subject, $message, Pickle\Tools\Config::$devmail);
        }
    }
}

//App::activeMiddlewares(['mustBeConnected']);

echo Router::run($url);

App::extra();

App::getEnvironnementTools();