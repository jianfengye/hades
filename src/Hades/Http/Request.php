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
    // request uri
    protected $uri;
    // request header
    protected $header;

    // create a Request from 
    public static function create()
    {
        $request = new Request;
        
        $request->get = $_GET;
        $request->post = $_POST;
        $request->server = $_SREVER;
        $request->cookie = $_COOKIE;
        $reuqest->header = getallheaders();

        return $request;
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
        if (isset($this->request[$key])) {
            return $this->request[$key];
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