<?php

namespace Hades\Http;

class Request
{
    // generate from $_GET
    protected $get;
    // generate from $_POST
    protected $post;
    // generate from $_SERVER
    protected $server;
    // generate from $_COOKIE
    protected $cookie;
    // request header
    protected $header;

    // after route
    protected $routeParams;

    // create a Request from 
    public static function create()
    {
        $request = new Request;
        
        $request->get = $_GET;
        $request->post = $_POST;
        $request->server = $_SERVER;
        $request->cookie = $_COOKIE;
        $reuqest->header = self::getRawHeaders();

        return $request;
    }

    private static function getRawHeaders()
    {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) != 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    // set RouteParam
    public function setRouteParam($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    // data from routeParams
    public function route($key, $default = null)
    {
        if (isset($this->routeParams[$key])){
            return $this->routeParams[$key];
        }
        return $default;
    }

    // data from $_GET
    public function get($key, $default = null)
    {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
        return $default;
    }

    // data from $_POST
    public function post($key, $default = null)
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        return $default;
    }

    // data from $_REQUEST
    public function request($key, $default = null)
    {
        $request = array_merge($this->get, $this->post, $this->cookie, $this->routeParams);
        if (isset($request[$key])) {
            return $request[$key];
        }
        return $default;
    }

    // data from cookie
    public function cookie($key, $default)
    {
        if (isset($this->cookie[$key])) {
            return $this->cookie[$key];
        }
        return $default;
    }

    // get request uri
    public function uri()
    {
        // in iis and forward proxy, uri in X_ORIGINAL_URL
        if (isset($this->server['X_ORIGINAL_URL']) && !empty($this->server['X_ORIGINAL_URL'])) {
            return $this->server['X_ORIGINAL_URL'];
        }

        return $this->server['REQUEST_URI'];
    }

    // get http request method
    public function method()
    {
        return strtoupper($this->server['REQUEST_METHOD']);
    }
}