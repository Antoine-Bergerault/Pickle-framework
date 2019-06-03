<?php
namespace Pickle\Engine\SessionChecker;
require_once 'SessionCommand.php';

class Module{

    public function act($data){
        foreach($data as $s){
            $name = $s->name;
            $data = $s->content;
            $content = $data->content;
            $command = new \Pickle\Engine\SessionChecker\SessionCommand();
            $command->rebuild($content);
            $app = $data->app;
            if(!isset($_SESSION["APP_SESSIONS_VERSION"])){
                $_SESSION["APP_SESSIONS_VERSION"] = [];
            }
            $versions = $_SESSION["APP_SESSIONS_VERSION"];
            if(!isset($versions[$name]) || $versions[$name] != $app['id']){
                $this->execute($name, $command);
                $_SESSION["APP_SESSIONS_VERSION"][$name] = $app['id'];
            }
        }
    }

    private function execute($name, $command){
        $commands = [
            'update',
            'clear',
            'delete'
        ];
        if($command->isActive() && in_array($command->action, $commands)){
            $new = $command->getNewValue();
            $_SESSION[$name] = $new;
        }
    }

}