<?php

class CsrfMiddleware {

    const KEY = '_csrf';

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateToken($length = 20){
        $token = bin2hex(random_bytes($length));
        App::add(self::KEY, $token);
        return $token;
    }

    public function verifyToken(){
        $token = $this->getToken();
        $r = false;
        if(isset($token)){
            $r = in_array($token, App::get(self::KEY));
        }
        if($r == false){
            throw new Exception('CSRF token not valid');
        }
        $tokens = App::get(self::KEY);
        $tokens = array_filter($tokens, function($t) use ($token) {
            return $token != $t;
        });
        App::save(self::KEY, $tokens);
        return true;
    }

    public function getToken(){
        $method = App::getMethod();
        $arr = [];
        if($method == 'POST'){ $arr = $_POST; }
        if($method == 'GET'){ $arr = $_GET; }
        if(isset($arr[self::KEY])){
            return $arr[self::KEY];
        }
        return null;
    }

    public function csrf_input(){
        echo '<input type="hidden" name="'. self::KEY .'" value="'. $this->generateToken() .'">';
    }

}