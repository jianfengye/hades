<?php

use Hades\Log\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testlog()
    {
        $file = Logger::instance()->file();
        if (file_exists($file)) {
            unlink($file);
        }

        Logger::instance()->info("test", ['field1' => 'value1']);
        $this->assertTrue(file_exists($file));
        unlink($file);
    }

    public function testRotate()
    {
        Logger::rotate(30);
        $folder = Logger::folder();
        $old_file = $folder . "/hades_2001-01-01.log";

        $file = Logger::instance()->file();
        Logger::instance()->info("test", ['field1' => 'value1']);

        $date = date("Y-m-d");
        $file = $folder . "/hades_" . $date . ".log";
        $this->assertTrue(file_exists($file));
        $this->assertFalse(file_exists($old_file));

        unlink($file);
    }
}
