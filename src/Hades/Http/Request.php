<?php

namespace Hades\Http;

use Hades\Facade\Facade;

class Request
{
    use Facade;

    public static function getAlias()
    {
        return 'Request';
    }

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
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
        $this->header = self::getRawHeaders();
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
    protected function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    // data from routeParams
    protected function route($key, $default = null)
    {
        if (isset($this->routeParams[$key])){
            return $this->routeParams[$key];
        }
        return $default;
    }

    // data from $_GET
    protected function get($key, $type = 'string', $default = null)
    {
        $value = $default;
        if (isset($this->get[$key])) {
            $value = $this->get[$key];
        }
        settype($value, $type);
        return $value;
    }

    // data from $_POST
    protected function post($key, $type = 'string', $default = null)
    {
        $value = $default;
        if (isset($this->post[$key])) {
            $value = $this->post[$key];
        }
        settype($value, $type);
        return $value;
    }

    // data from $_REQUEST
    protected function request($key, $type = 'string', $default = null)
    {
        $request = array_merge($this->get, $this->post, $this->cookie, $this->routeParams);
        $value = $default;
        if (isset($request[$key])) {
            $value = $request[$key];
        }
        settype($value, $type);
        return $value;
    }

    // data from cookie
    protected function cookie($key, $default)
    {
        if (isset($this->cookie[$key])) {
            return $this->cookie[$key];
        }
        return $default;
    }

    // get request uri
    protected function uri()
    {
        // in iis and forward proxy, uri in X_ORIGINAL_URL
        if (isset($this->server['X_ORIGINAL_URL']) && !empty($this->server['X_ORIGINAL_URL'])) {
            return $this->server['X_ORIGINAL_URL'];
        }

        return $this->server['REQUEST_URI'];
    }

    // get http request method
    protected function method()
    {
        return strtoupper($this->server['REQUEST_METHOD']);
    }

    // create request by create
    protected function create($method, $params)
    {
        if (strtolower($method) == 'get') {
            $this->get = $params;
        } else {
            $this->post = $params;
        }
        return $this;
    }
}
