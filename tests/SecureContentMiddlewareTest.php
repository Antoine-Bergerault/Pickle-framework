<?php

use Pickle\Engine\App;
use PHPUnit\Framework\TestCase;

class SecureContentMiddlewareTest extends TestCase{

    public function testNo_script(){
        $middleware = App::middleware('secureContent');
        $this->assertEquals($middleware->to_html('<h1>Test</h1>'), $middleware->no_script('<h1>Test</h1><script>alert(\'test\')</script>'));
    }

}