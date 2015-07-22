<?php

namespace Hades\Exception;

class ExceptionHandler
{
    private $exception;

    public function __constrct($exception)
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
        if (php_sapi_name() == 'cli') {
            return $this->renderConsole();
        } else {
            return $this->renderHttp();
        }
    }
}
