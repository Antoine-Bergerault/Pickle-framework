<?php
namespace Pickle\Engine;


class Route{

    public $path = null;//path to access this route
    public $callback;//function used as callback
    public $matches = [];//advanced matches rules
    public $associativesMatches = [];
    public $params = [];
    public $cond = false;
    public $view = false;
    public $refresh = false;
    public $name = false;
    public $include = false;
    public $data = [];
    public $cache = false;
    public $amp = false;
    public $mobileversion = false;

    /**
     * Route constructor.
     * @param $path string
     * @param $callback function || string
     */
    public function __construct(string $path, $arr = []){//set the callback and path
        if(!is_array($arr)){
            $arr = [
                'script' => $arr
            ];
        }
        $arr = array_replace([
            'view' => false,
            'script' => false,
            'reload' => false,
            'name' => trim($path,'/'),
            'include' => false,
            'data' => [],
            'cache' => false,
            'amp' => false
        ],$arr);
        $this->path = $path;
        $this->view = $arr['view'];
        $this->callback = $arr['script'];
        $this->refresh = $arr['reload'];
        $this->name = $arr['name'];
        $this->include = $arr['include'];
        $this->data = $arr['data'];
        $this->cache = $arr['cache'];
        $this->mobileversion = $arr['amp'];
        return $this;
    }

    public function only_if($a,$callback = false){//add a condition to enable this route
        $this->cond = true;
        if($a !== true){
            if($callback == false){
                $this->path = false;
            }else{
                $this->callback = $callback;
            }
        }
        return $this;
    }

    public function with($param, $regex){//used for advanced regex for url parameters
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    public function move($beg){//add at the beginning of the path. (used for grouping)
        if($this->path != false){
            $this->path = $beg.$this->path;
        }
        return $this;
    }

    private function callback_to_func($callback){//if callback is a Controller method, translate it
        $callback = explode('@', $callback);
        $controller = '\\Pickle\\'.$callback[0];
        $method = $callback[1];
        $Controller = new $controller();
        return array($Controller,$method);//$Controller->$method() will be executed
    }

    public function match($url){//verify if matching
        if($this->path == false){
            return false;
        }
        $this->associativesMatches = [];
        $url = rtrim($url,'/').'/';
        $this->path = rtrim($this->path,'/').'/';
        $path = preg_replace_callback('#{([\w]+)}#', [$this, 'checkParams'], $this->path);
        $regex = "#^$path$#i";
        if(!preg_match($regex, $url, $matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        $this->associativesMatches = array_combine($this->associativesMatches, $this->matches);
        return true;
    }

    private function checkParams($match){//check if the parameters are good

        $this->associativesMatches[] = $match[1];

        if(isset($this->params[$match[1]])){
            return '('.$this->params[$match[1]].')';
        }

        return '([^/]+)';
    }

    public function callback(){//return the callback function
        $cache = false;
        if($this->cache == true){
            $cache = new Cache();
            $res = false;
            if($res = $cache->read($this->cachename())){
                return json_decode($res);
            }
        }
        $callback = $this->callback;
        if(is_string($callback)){
            $callback = $this->callback_to_func($callback);
        }
        $res = call_user_func_array($callback, $this->matches);
        if($this->cache == true){
            $cache->write($this->cachename(),json_encode($res));
            return $res;
        }
        return $res;
    }

    public function cachename(){
        $name = $this->path;
        $name = str_replace('/','_',$name);
        $name = str_replace('{','',$name);
        $name = str_replace('}','',$name);
        return 'scripts/'.$name;
    }

    public function isAmpAvailable(){
        return $this->mobileversion;
    }

    public function setAmp($val, $path){
        $this->amp = $val;
        $this->move($path);
        return $this;
    }

}

?>