<?php

namespace Hades\Test;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function callAction($controllerAction, $request)
    {
        list($controller, $action) = explode('@', $controllerAction);
        $c = new $controller;
        return call_user_func([$c, $action], $request);
    }
}
