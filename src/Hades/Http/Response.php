<?php

namespace Hades\Http;

class Response
{
    // header
    protected $headers;
    // body
    protected $body;

    // set header
    public function addHeader($key, $value)
    {

    }

    public function setBody($body)
    {

    }

    public function sendHeader()
    {

    }

    public function sendBody()
    {

    }

    public function send()
    {
        $this->sendHeader();
        $this->sendBody();
    }
}