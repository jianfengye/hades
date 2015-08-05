<?php

use Hades\Route\Manager as Route;

class ManagerTestController
{
    function index1($request)
    {
        return 'index1';
    }

    function index2($request)
    {
        return 'index2';
    }

    function index3($request)
    {
        return 'index3';
    }
}

class ManagerTestMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);
        return $response . '_add_middleware';
    }
}

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testRoute()
    {
        Route::get('/test/index1', 'ManagerTestController@index1');
        Route::post('/test/index2', 'ManagerTestController@index2');

        Route::group(['middleware' => ['ManagerTestMiddleware']], function(){
            Route::get('/test/index3', 'ManagerTestController@index3');
        });

        class_alias('\Hades\Http\Request', 'TestRequest');
        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/index1');

        $response = Route::dispatch($request);
        $this->assertEquals('index1', $response);

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('POST');
        $request->method('uri')->willReturn('/test/index2');

        $response = Route::dispatch($request);
        $this->assertEquals('index2', $response);

        $request = $this->getMockBuilder('TestRequest')->getMock();
        $request->method('httpmethod')->willReturn('GET');
        $request->method('uri')->willReturn('/test/index3');

        $response = Route::dispatch($request);
        $this->assertEquals('index3_add_middleware', $response);
    }
}
