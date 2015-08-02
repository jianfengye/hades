<?php
if (file_exists('/tmp/a.log')) {
    unlink('/tmp/a.log');
}

$resource = fopen('/tmp/a.log', 'c');
//system("rm /tmp/a.log");
unlink('/tmp/a.log');
$a = stat('/tmp/a.log');
print_r($a);exit;
$ret = fwrite($resource, '111111');
if (false == $ret) {
    $resource = fopen('/tmp/a.log', 'c');
    fwrite($resource, '111111');
}


class Log
{
    private function __construct() {}

    public static function instance($file)
    {
        $instance = new static();
        $instance->stream = fopen($file, 'w');
        return $instance;
    }

    public function write($content)
    {
        fwrite($this->stream, $content);
    }

    public function close()
    {
        fclose($this->stream);
    }
}

class Log2
{
    private function __construct() {}

    public static function instance($file)
    {
        $instance = new static();
        $instance->file = $file;
        return $instance;
    }

    public function write($content)
    {
        $stream = fopen($this->file, 'c');
        fwrite($stream, $content);
        fclose($$stream);
    }
}
