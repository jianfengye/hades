<?php

namespace Hades\Route;

use Hades\Container\Container;
use Hades\Facade\Facade;

// manager Route
class Manager extends Facade
{
    private $routes = [];

    public function getAlias()
    {
        return "Route";
    }

    // return response
    public function dispatch(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($route->match($request)) {
                return $route->action($request);
            }
        }
    }

    // set get route
    public function get($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = closure($callback);
        }

        $this->routes[] = new Route('get', $uri, $callback);
    }

    // set post route
    public function post($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = closure($callback);
        }

        $this->routes[] = new Route('post', $uri, $callback);
    }

    // set any route
    public function any($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = closure($callback);
        }

        $this->routes[] = new Route('get', $uri, $callback);
        $this->routes[] = new Route('post', $uri, $callback);
    }

    // convert string to closure
    private function closure($controllerAction)
    {
        list($controller, $action) = explode('@', $controllerAction);
        return function($request) use ($controller, $action) {
            $c = new $controller;
            return call_user_func([$c, $action], $request);
        };
    }
}