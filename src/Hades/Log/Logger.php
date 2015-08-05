<?php
namespace Hades\Log;

class Logger
{
    // set logger rotate
    private static $rotate = 0;

    private static $folder = '';

    private static $psrLoggers;


    public static function setFolder($folder)
    {
        self::$folder = $folder;
    }

    public static function folder()
    {
        return self::$folder;
    }

    public static function rotate($day = 30)
    {
        self::$rotate = 30;
    }

    public static function filename($name)
    {
        $file = self::$folder . '/' . $name;
        if (self::$rotate) {
            $file .= '_' . date("Y-m-d");
        }
        $file .= ".log";
        return $file;
    }

    public static function instance($name = 'hades')
    {
        $file = self::filename($name);
        if (isset(self::$psrLoggers[$file])) {
            return self::$psrLoggers[$file]['instance'];
        }

        $psrLogger = ['basename' => $name, 'instance' => new Psr($file), 'date' => date('Y-m-d') ];
        self::$psrLoggers[$file] = $psrLogger;

        // Do GC
        if (self::$rotate) {
            self::gc($name);
        }

        return self::$psrLoggers[$file]['instance'];
    }

    // gc the roate files
    public static function gc($name)
    {
        $files = glob(self::$folder . "/{$name}_*.log");
        foreach ($files as $filename) {
            $date = str_replace(self::$folder . "/{$name}_" , "", $filename);
            $date = str_replace(".log", "", $date);
            if (time() - strtotime($date) > self::$rotate *  3600 * 24) {
                unlink($filename);
            }
        }
    }
}
