<?php

class AjaxModule{

    public function generateForm(string $path, array $settings, array $arr, string $method = 'post'){
        $callback = $settings['callback'] ?? 'location.reload';

        $path = "ajax$path";

        $obj = $this->dynamicAccess($arr);
        $do = '';
        if($method == 'post'){
            $action = "$.post('$path', $obj, function(data){ $callback(data) });";
        }else if($method == 'get'){
            $action = "$.get('$path', $obj, function(data){ $callback(data) });";
        }
        if(isset($settings['function'])){
            $do .= 'function '.$settings['function'].'(){ '.$action.' };';
        }
        if(isset($settings['event'])){
            $do .= "$('".$settings['event']."').click(function(){ $action });";
        }
        $this->script($do);
        return $this;
    }

    public function action(string $path, array $settings, string $method = 'post'){
        $path = "ajax$path";
        if ($method == 'post') {
            $action = "$.post('$path');";
        } else if ($method == 'get') {
            $action = "$.get('$path');";
        }
        $do = '';
        if (isset($settings['event'])) {
            $do .= "$('" . $settings['event'] . "').click(function(){ $action });";
        }
        $this->script($do);
        return $this;
    }

    private function dynamicAccess(array $arr){
        $r = '{';
        foreach($arr as $k => $v){
            $r .= $k;
            $r .= ':';
            $r .= "$('$v').val(),";
        }
        $r = rtrim($r, ',');
        $r .= '}';
        return $r;
    }

    private function script(string $action){
        echo '<script>$(function(){';
        echo html_entity_decode($action);
        echo '});</script>';
    }

}