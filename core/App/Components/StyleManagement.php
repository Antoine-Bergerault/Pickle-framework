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
        return $color == false ? '' : $color;
    }

}