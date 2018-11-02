<?php

class TestMiddlewareTest extends PHPUnit_Framework_TestCase{

    public function testTest(){
        $this->assertEquals('test', TestMiddleware::test());
    }

}