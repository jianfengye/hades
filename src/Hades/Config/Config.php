<?php

namespace Hades\Config;

Class Config
{
    private static $folder = '';

    public static function setFolder($folder)
    {
        self::$folder = $folder;
    }

    public static function get($paths, $default = null)
    {
        $paths = explode('.', $paths);

        $file = self::$folder . "/" . $paths[0] . ".php";
        if (!file_exists($file)) {
            return $default;
        }

        $config = require $file;

        $value = $config;
        foreach ($paths as $key => $path) {
            if ($key == 0) {
                continue;
            }

            if (isset($value[$path])) {
                $value = $value[$path];
            } else {
                return $default;
            }
        }
        return $value;
    }
}
