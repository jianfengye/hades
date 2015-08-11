<?php

namespace Hades\Route;

use Hades\Http\Request;
use Hades\Route\Exception\NotFoundRouteException;

// manager Route
class Manager
{
    private static $routes = [];

    private static $group_params = [];

    // return response
    public static function dispatch(Request $request)
    {
        foreach (self::$routes as $route) {
            if ($route->match($request)) {
                return $route->action($request);
            }
        }
        throw new \LogicException("Not Found Route");
    }

    // set get route
    public static function get($uri, $callback, $params = [])
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        $middlewares = array_merge(self::middlewaresByParams($params), self::middlewaresByParams(self::$group_params));

        self::$routes[] = new Route('get', $uri, $callback, $middlewares);
    }

    // set post route
    public static function post($uri, $callback, $params = [])
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        $middlewares = array_merge(self::middlewaresByParams($params), self::middlewaresByParams(self::$group_params));

        self::$routes[] = new Route('post', $uri, $callback, $middlewares);
    }

    // set any route
    public static function any($uri, $callback, $params = [])
    {
        if (is_string($callback)) {
            $callback = self::str2closure($callback);
        }

        $middlewares = array_merge(self::middlewaresByParams($params), self::middlewaresByParams(self::$group_params));

        self::$routes[] = new Route('get', $uri, $callback, $middlewares);
        self::$routes[] = new Route('post', $uri, $callback, $middlewares);
    }

    // group the routes
    public static function group($params, $callback)
    {
        self::$group_params = $params;
        $callback();
        self::$group_params = [];
    }

    // get middlewares
    private static function middlewaresByParams($params)
    {
        if (empty($params['middleware'])) {
            return [];
        }
        return $params['middleware'];
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
