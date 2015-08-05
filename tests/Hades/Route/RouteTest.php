<?php

use Hades\Route\Manager as Manager;
use Hades\Route\Route;

class TestController
{
    function index($request)
    {
        return 'bar';
    }

    function routeParam($request)
    {
        return $request->getRouteParams();
    }
}

class TestMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);
        return $response . '_add_middleware';
    }
}

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        if (!class_exists('TestRequest')) {
            class_alias('\Hades\Http\Request', 'TestRequest');
        }
    }

    public function testRouteRegex()
    {
        $callback = function($request) {
            $c = new TestController();
            return call_user_func([$c, 'routeParam'], $request);
        };
        $route = new Route('get', '/test/{id}', $callback, []);

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/1');
        $this->assertEquals(true, $route->match($request));

        $response = $route->action($request);
        // TODO: 无法测试
        //$this->assertEquals(1, $response['id']);
    }

    public function testRoute()
    {
        $callback = function($request) {
            $c = new TestController();
            return call_user_func([$c, 'index'], $request);
        };
        $route = new Route('get', '/test/index', $callback, ['\TestMiddleware']);

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/index2');
        $this->assertEquals(false, $route->match($request));

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('POST');
        $request->method('uri')->willReturn('/test/index');
        $this->assertEquals(false, $route->match($request));

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/index');
        $this->assertEquals(true, $route->match($request));

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/index');
        $response = $route->action($request);
        $this->assertEquals('bar_add_middleware', $response);
    }


}
