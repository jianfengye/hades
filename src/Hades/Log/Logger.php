<?php
namespace Hades\Log;

class Logger
{
    use Psr\Log\LoggerTrait;
    use Hades\Facade\Facade;

    // set logger rotate
    private $rotate = 0;

    private $file_name = '';

    public function setRotate($day = 30)
    {
        $this->rotate = 30;
    }

    public function log($level, $message, array $context = array())
    {

    }
}
