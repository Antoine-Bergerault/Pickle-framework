<?php
namespace Pickle\Engine;

class Cache {

    public $dirname;
    public $duration;//in minutes
    private $folders = [];

    public function __construct($duration = 10){

        $this->duration = $duration;
        $this->dirname = Config::$CacheDirectory;
        $this->folders = $this->getfolders();

    }

    public function read($filename){

        $file = "./$this->dirname/".$filename;
        if(!file_exists($file)){
            return false;
        }
        $lifetime = (time() - filemtime($file)) / 60;
        if($lifetime > $this->duration){
            if($filename == 'home'){
                $this->clearfolder('load');
            }
            return false;
        }
        return file_get_contents($file);
        
    }

    public function write($filename, $content){

        return file_put_contents("./$this->dirname/".$filename,$content);

    }

    public function delete($filename){
        $file = "./$this->dirname/".$filename;
        if(file_exists($file)){
            unurl($file);
        }
    }

    public function clear(){
        $files = glob("./$this->dirname/*");
        foreach($files as $file){
            unurl($file);
        }

        foreach($this->folders as $folder){
            $this->clearfolder($folder);
        }

    }

    public function clearfolder($name){
        $files = glob("./$this->dirname/$name/*");
        foreach($files as $file){
            unurl($file);
        }
    }

    public function folder($dirname){
        if (!file_exists("./$this->dirname/$dirname")) {
            mkdir("./$this->dirname/$dirname");
        }
        $this->folders[] = $dirname;
    }

    public function getfolders(){

        $path = "./$this->dirname/";
        $dirs = array();
    
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $dirs[] = $file->getFilename();
            }
        }
    
        return $dirs;
    }

}

?>