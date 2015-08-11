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

    protected function renderHttp() {}

    protected function renderConsole() {}

    public function render()
    {
        $log = [
            "File" => $this->exception->getFile(),
            "Line" => $this->exception->getLine(),
            "Trace" => PHP_EOL . $this->exception->getTraceAsString(),
        ];
        Logger::instance()->error($this->exception->getMessage(), $log);

        if (php_sapi_name() == 'cli') {
            return $this->renderConsole();
        } else {
            return $this->renderHttp();
        }
    }
}
