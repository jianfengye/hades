<?php

namespace Hades\Route;

use Hades\Http\Request;

class Route 
{
    private $uri;

    private $callback;

    private $method;

    public function __construct($method, $uri, $callback)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->callback = $callback;
    }

    // check is match
    public function match(Request $request)
    {
        $method = $request->method();
        if (strtoupper($method) != strtoupper($this->method)) {
            return false;
        }
        // TODO: check uri
        $regex = $this->uriRegex($this->uri);
        $regex = preg_quote($regex, '/');
        $match = preg_match("/{$regex}/", $request->uri(), $matches);
        if (!$match) {
            return false;
        }

        if ($matches[0] != $request->uri()) {
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
        return preg_replace(['/{(\w+)}/', '/{(\w+)\?}/', '/{(\w+)\*}/'], ['(?P<$1>\w+)', '(?P<$1>\w?)', '(?P<$1>\w*)'], $uri);
    }

    public function action($request)
    {
        $callback = $this->callback;
        return $callback($request);
    }
}