<?php
namespace Hades\Log;
use Psr\Log\LoggerTrait;

class Psr
{
    use LoggerTrait;

    private $file;

    private $stream;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function file()
    {
        return $this->file;
    }

    public function log($level, $message, array $context = array())
    {
        $line = date("Y-m-d H:i:s") . " {$level}:{$message}\r\n" . var_export($context, true) . "\r\n";
        $stream = fopen($this->file, 'a');
        fwrite($stream, $line);
        fclose($stream);
    }

    public function destory()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}
