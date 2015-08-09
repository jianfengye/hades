<?php

namespace Hades\Exception;
use Hades\Log\Logger;

class ExceptionHandler
{
    private $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function renderHttp()
    {

    }

    public function renderConsole()
    {

    }

    public function render()
    {
        // TODO: 优化这里的错误格式
        Logger::instance()->error($this->exception->getMessage(), []);

        if (php_sapi_name() == 'cli') {
            return $this->renderConsole();
        } else {
            return $this->renderHttp();
        }
    }
}
