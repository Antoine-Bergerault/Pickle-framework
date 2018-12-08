<?php
namespace Pickle\Engine\Builder;

use Pickle\Engine\App;


class AppBuilder{

    public $env = false;
    private $styles = [];
    private $elements = [];

    public function loadEnvironnement(string $env){

        $this->env = $env;

        if($env == 'DEV'){
            $this->createToolBar();
        }

    }

    public function print(){
        foreach($this->elements as $el){
            echo $el;
        }
        if(sizeof($this->styles) > 0){
            echo '<style>';
            foreach($this->styles as $style){
                echo $style;
            }
            echo '</style>';
        }
    }

    public function add(string $html, $css = null){
        $this->elements[] = $html;
        if($css != null){
            $this->styles[] = $css;
        }
    }

    public function createToolBar(){
        $this->add(
        '<div id="Pickle-tools-navbar">
            <p style="float:left">'.App::$url.'</p>
            <p style="float:right">'.($GLOBALS['Pickle-DB-QueryCount'] ?? '0').' queries</p>
        </div>', '
            #Pickle-tools-navbar{
                display: block;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 25px;
                line-height: 25px;
                background: #222;
                color: #ddd;
            }
            #Pickle-tools-navbar p{
                margin: 0 0;!important
                padding: 0 0;!important
            }
        ');
        return $this;
    }

}