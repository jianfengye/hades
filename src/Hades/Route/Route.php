<?php

namespace Hades\Route;

use Hades\Http\Request;

class Route
{
    private $uri;

    private $callback;

    private $method;

    private $middlewares;

    public function __construct($method, $uri, $callback, $middlewares)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->callback = $callback;
        $this->middlewares = $middlewares;
    }

    // check is match
    public function match(Request $request)
    {
        $method = $request->httpmethod();
        if (strtoupper($method) != strtoupper($this->method)) {
            return false;
        }
        // TODO: check uri
        $regex = $this->uriRegex($this->uri);

        $path = parse_url(trim($request->uri(), '#'), PHP_URL_PATH);
        $match = preg_match("/{$regex}/", $path, $matches);
        if (!$match) {
            return false;
        }

        if ($matches[0] != $path) {
            return false;
        }

        $request->setRouteParams($matches);

        return true;
    }

    private function uriRegex($uri)
    {
        // change {id} to ([\S]+)
        // change {id?} to ([\S]?)
        // change {id*} to ([\S]*)
        $uri = str_replace('/', '\/', $uri);
        return preg_replace(['/{(\w+)}/', '/{(\w+)\?}/', '/{(\w+)\*}/'], ['(?<$1>\w+)', '(?<$1>\w?)', '(?<$1>\w*)'], $uri);
    }

    public function action($request)
    {
        $callback = $this->callback;
        $middlewares = $this->middlewares;

        return call_user_func(
            array_reduce($middlewares, function($stack, $middleware){
                return function($request) use ($stack, $middleware) {
                    if ($middleware instanceof Closure) {
                        return call_user_func($middleware, $request, $stack);
                    } else {
                        $middlewareClass = new $middleware;
                        return $middlewareClass->handle($request, $stack);
                    }
                };
            }, $callback),
        $request);
    }
}
