<?php
namespace Pickle\Engine;


trait StyleManagement{

    static function getColors(){
        $cache = new Cache(false);
        $colors = $cache->read('colors') == false ? [] : unserialize($cache->read('colors'));
        return $colors;
    }

    static function setColors($colors){
        $cache = new Cache(false);
        $cache->write('colors', serialize($colors));
    }

    static function color($name){
        $color = $_SESSION['colors'][$name] ?? false;
        if($color == false){
            $color = $_SESSION['colors'][\slugify($name)] ?? false;
        }
        if(!isset($GLOBALS['Pickle-Used-Colors'])){
            $GLOBALS['Pickle-Used-Colors'] = [];
        }
        if(!isset($GLOBALS['Pickle-Used-Colors'][$name])){
            $GLOBALS['Pickle-Used-Colors'][$name] = $color;
        }
        return $color == false ? '' : $color;
    }

}