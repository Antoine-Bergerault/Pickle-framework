<?php

use Pickle\Engine\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase{

    public function testIsConnected(){
        $this->assertEquals(false, App::is_connected());
    }

    public function testMiddleware(){
        require 'src/Middlewares/CsrfMiddleware.php';
        $csrfmiddleware = new \CsrfMiddleware();
        $this->assertEquals($csrfmiddleware, App::middleware('csrf'));
    }

    public function testGetMethod(){
        $this->assertEquals($_SERVER['REQUEST_METHOD'], App::getMethod());
    }

}