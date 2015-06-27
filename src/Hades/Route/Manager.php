<?php

namespace Hades\Route;

use Hades\Http\Request;

// manager Route
class Manager
{
    private static $routes = [];

    // return response
    public static function dispatch(Request $request)
    {
        foreach (self::$routes as $route) {
            if ($route->match($request)) {
                return $route->action($request);
            }
        }
    }

    // set get route
    public static function get($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        self::$routes[] = new Route('get', $uri, $callback);
    }

    // set post route
    public static function post($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        self::$routes[] = new Route('post', $uri, $callback);
    }

    // set any route
    public static function any($uri, $callback)
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        self::$routes[] = new Route('get', $uri, $callback);
        self::$routes[] = new Route('post', $uri, $callback);
    }

    // convert string to closure
    private static function str2closure($controllerAction)
    {
        list($controller, $action) = explode('@', $controllerAction);
        return function($request) use ($controller, $action) {
            $c = new $controller;
            return call_user_func([$c, $action], $request);
        };
    }
}