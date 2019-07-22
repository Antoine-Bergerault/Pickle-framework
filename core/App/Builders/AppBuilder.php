<?php
namespace Pickle\Engine\Builder;

use Pickle\Engine\App;


class AppBuilder{

    public $env = false;
    private $styles = [];
    private $scripts = [];
    private $elements = [];

    public function loadEnvironnement(string $env){

        $this->env = $env;

        if($env == 'DEV'){
            $this->createToolBar();
        }else if($env = 'RESTRICTED' && App::is_role('admin')){
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
        if(sizeof($this->scripts) > 0){
            echo '<script>';
            foreach($this->scripts as $script){
                echo $script;
            }
            echo '</script>';
        }
    }

    public function add(string $html, $css = null, $js = null){
        $this->elements[] = $html;
        if($css != null){
            $this->styles[] = $css;
        }
        if($js != null){
            $this->scripts[] = $js;
        }
    }

    public function createToolBar(){
        $html = 
        '<div id="Pickle-tools-navbar">
            <div id="pickle-nav-infos">
                <p style="float:left">'.'/'.$_GET['picklerewriteurl'].'</p>
                <p style="float:right">'.($GLOBALS['Pickle-DB-QueryCount'] ?? '0').' queries</p>
            </div>
            <div id="pickle-tools-content">
                <div id="pickle-tools-queries">
                    ';
                    
        foreach($GLOBALS['Pickle-DB-Queries'] ?? [] as $i => $query){
            $html .= '<div style="margin:20px 0;padding:10px;background:#eee;color:#000;box-sizing:border-box;">';
            $html .= '<h4 style="font-size: 0.8em;">'.str_replace('?', '<span style="color:red">?</span>', $query).'</h4>';
            $html .= '<table style="border:1px solid black;border-radius:2px;overflow:hidden;">';
            $html .= '<tr>
                        <th>Index</th>
                        <th>Valeur</th>
                    </tr>';
            foreach($GLOBALS['Pickle-DB-Params'][$i] as $k => $v){
                $html .= '<tr>
                            <td style="color:#000">'.$k.'</td>
                            <td style="color:#000">'.\htmlentities($v).'</td>
                        </tr>';
            }
            $html .= '</table>';
            $html .= '</div>';
        }

        $html .= '
                </div>';

        $html .= '<div id="pickle-tools-user">';
        
        $html .= 'Connecté : ' . (App::is_connected() ? 'oui' : 'non');

        $html .= '</div>';

        if(isset($GLOBALS['Pickle-Used-Colors'])){
            $html .= 'Couleurs utilisées';
            $html .= '<table style="border:1px solid black;border-radius:2px;overflow:hidden;">';
            $html .= '<tr>
                        <th>Nom</th>
                        <th>Valeur</th>
                    </tr>';
            foreach($GLOBALS['Pickle-Used-Colors'] ?? [] as $k => $v){
                $html .= '<tr>
                            <td style="color:#000">'.$k.'</td>
                            <td style="color:#000">'.\htmlentities($v).'<div style="display:inline-block;margin-left:5px;height:15px;width:15px;border-radius:2px;background-color:'.$v.'"></div></td>
                        </tr>';
            }
            $html .= '</table>';
            $html .= '</div>';
        }

        $html .= '
            </div>
        </div>';
        $this->add($html, '
            #Pickle-tools-navbar{
                display: block;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 55px;
                line-height: 25px;
                background: #222;
                color: #ddd;
                transition: 0.4s;
                z-index: 10;
                padding: 15px;
                box-sizing: border-box;
            }
            #Pickle-tools-navbar p{
                margin: 0 0;!important
                padding: 0 0;!important
            }
            #Pickle-tools-navbar.active{
                height: 100%;
                overflow-y: auto;
            }
            #pickle-nav-infos{
                height: 25px;
                cursor: pointer;
            }
            #Pickle-tools-navbar.active #pickle-tools-content{
                display: block;
            }
            #pickle-tools-content{
                display: none;
                margin-top: 15px;
            }
            #pickle-tools-content table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
              
            #pickle-tools-content td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
            #pickle-tools-content tr{
                background-color: #777;
            }
            #pickle-tools-content tr:nth-child(even) {
                background-color: #dddddd;
            }
        ', '
            window.onload = function(){
                document.getElementById("pickle-nav-infos").addEventListener("click", function(){
                    let c = document.getElementById("Pickle-tools-navbar").classList.contains("active");
                    if(c){
                        document.getElementById("Pickle-tools-navbar").classList.remove("active");
                    }else{
                        document.getElementById("Pickle-tools-navbar").classList.add("active");
                    }
                });
            }
        ');
        return $this;
    }

}