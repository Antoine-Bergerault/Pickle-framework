<?php
namespace Pickle\Engine;

class Extras{

    public function debug($a){
        $result = "<div style='position: absolute;top:50%;left:50%;transform:translate(-50%,-50%);padding:5px'>$a</div>";
        return $result;
    }

    public function flashes($flashes){
        $elem = '';
        foreach($flashes as $flash){
            $elem .= "<div class='flash'>$flash <div class='flash-delete'>Delete</div></div>";
        }

        $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://"). "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $url = '/';
        if(isset($_GET['url'])){//set the url
            $url = '/'.$_GET['url'];
        }

        $root = explode($url, $root);
        $root = $root[0];
        
        $root = trim($root, '/');

        $elem .= '<script>for (let elem of document.querySelectorAll(\'.flash-delete\')){
                            elem.addEventListener(\'click\',function(){
                                this.parentElement.style.display = \'none\';
                            });
                          }
                  </script>';
        return $elem;
    }

}

?>