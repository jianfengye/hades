<?php

namespace Hades\Http;

class Response
{
    // header
    protected $headers = [];
    // cookie
    protected $cookies = [];
    // body
    protected $body;
    // status code
    protected $statusCode = 200;
    // charset;
    protected $charset = 'UTF-8';
    // contentType
    protected $contentType = 'text/html';

    // data
    private $data;

    public static $share = [];

    public $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    // set header
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function addCookie($key, $value)
    {
        $this->cookies[$key] = $value;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function sendHeader()
    {
        if (headers_sent()) {
            return $this;
        }

        $statusText = (isset($this->statusTexts[$this->statusCode])) ? : "";

        header(sprintf('HTTP/1.1 %s %s', $this->statusCode, $statusText), true, $this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value, false, $this->statusCode);
        }

        // TODO: send cookie header
    }

    public function sendBody()
    {
        echo $this->body;
        return $this;
    }

    public function send()
    {
        $this->sendHeader();
        $this->sendBody();
        return $this;
    }

    public function getRawData()
    {
        return $this->data;
    }

    public function setRawData($data)
    {
        $this->data = $data;
        return $this;
    }

    public static function share(array $share)
    {
        self::$share = array_merge(self::$share, $share);
    }
}
