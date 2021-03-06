<?php
namespace Pickle\Engine;

class Router{
    
    static $routes = ['GET' => [], 'POST' => []];//list of routes
    static $default = false;//default route
    static $ajaxurl = '/load';
    static $amp = false;

    static function get($path, $arr = []){//add a route with the get method
        $route = new Route($path, $arr);
        self::$routes['GET'][] = $route;
        return $route;
    }

    static function post($path, $arr = []){//add a route with the post method
        $route = new Route($path, $arr);
        self::$routes['POST'][] = $route;
        return $route;
    }

    static function group($path, $args){//group routes
        foreach($args as $arg){
            $arg->move($path);
        }
        return true;
    }

    static function condition(array &$routes, $condition){
        foreach($routes as $route){
            $route->only_if($condition);
        }
    }

    static function default_path($path){//add the default path
        self::$default = $path;
    }

    /**
     * @return string
     */
    static function run($url){//check if a route correspond with the url
        $method = $_SERVER['REQUEST_METHOD'];
        if(isset(self::$routes[$method])) {
            $GLOBALS['amp'] = false;
            for($i = 0; $i < sizeof(self::$routes[$method]); $i++) {
                $route = self::$routes[$method][$i];
                if($route->match($url) == true){
                    $GLOBALS['mobileversion'] = $route->isAmpAvailable();
                    if($route->view == false){
                        return $route->callback();
                    }
                    $data = $route->callback != false && $route->include == false;
                    $refresh = $route->refresh;
                    $GLOBALS['data'] = $data;
                    $GLOBALS['refresh'] = $refresh;
                    if($route->include == true){
                        return view($route->view, array_merge($route->callback(), $route->data, $route->associativesMatches));
                    }
                    \Pickle\Engine\App::save('AJAX_FROM_ROUTE', $route->path);
                    return view($route->view, array_merge(compact(['data','refresh']), $route->data, $route->associativesMatches));
                }
            }
        }
        if($method == 'GET' && trim($url,'/') == trim(self::$ajaxurl,'/')){
            $from = $_SERVER["HTTP_REFERER"];
            $from = \Pickle\Engine\App::get('AJAX_FROM_ROUTE');
            $root = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"];
            $path = str_replace($root,'',$from);

            foreach(self::$routes[$method] as $route){
                if($route->match($path) == true){
                    $arr = $route->callback();
                    \Pickle\Engine\App::disableComponents();
                    return json_encode($arr);
                }
            }
        }

        if(self::$amp != false){
            if(isset(self::$routes[$method])) {
                $GLOBALS['amp'] = true;
                for($i = 0; $i < sizeof(self::$routes[$method]); $i++) {
                    $route = self::$routes[$method][$i];
                    if($route->isAmpAvailable()){
                        $route->setAmp(true, self::$amp);
                        if($route->match($url) == true){
                            if($route->view == false){
                                return $route->callback();
                            }
                            $data = $route->callback != false && $route->include == false;
                            $refresh = $route->refresh;
                            $GLOBALS['data'] = $data;
                            $GLOBALS['refresh'] = $refresh;
                            if($route->include == true){
                                return view($route->view, array_merge($route->callback(), $route->data, $route->associativesMatches));
                            }
                            \Pickle\Engine\App::save('AJAX_FROM_ROUTE', $route->path);
                            return view($route->view, array_merge(compact(['data','refresh']), $route->data, $route->associativesMatches));
                        }
                    }
                }
            }
        }

        if(self::$default != false){//if there is a default and no matches
            redirect(url(self::$default));//redirect to the default path
            return false;
        }else{
            return view('errors/error404');
        }

    }

    /**
     * @return null
     */
    static function setAjax($url){
        self::$ajaxurl = $url;
    }

    /**
     * @return null
     */
    static function setAmp($path){
        self::$amp = $path;
    }

    /**
     * @return Route
     */
    static function getRoute($name){
        foreach(self::$routes['GET'] as $route){
            if($route->name == $name){
                return $route;
            }
        }
        return false;
    }

}


?>