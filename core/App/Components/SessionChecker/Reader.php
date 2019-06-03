<?php
namespace Pickle\Engine\SessionChecker;

class Reader{

    public $root = ROOT.'/core/App/Components/SessionChecker/data';

    public function read(){

        $data = [];
        $files = glob($this->root . '/*');
        foreach($files as $file){
            $content = file_get_contents($file);
            $name = str_replace($this->root . '/', '', $file);
            $obj = new \stdClass();
            $obj->name = $name;
            $obj->content = unserialize($content);
            $data[] = $obj;
        }

        return $data;
    }

}