<?php

namespace Hades\Config;

Class Config
{
    private static $folder = '';

    private static $local = [];

    public static function setFolder($folder)
    {
        self::$folder = $folder;
    }

    // load env.php
    public static function local($local)
    {
        $local = require $local;
        self::$local = $local;
    }

    public static function get($paths, $default = null)
    {
        $paths = explode('.', $paths);

        $file = self::$folder . "/" . $paths[0] . ".php";
        if (!file_exists($file)) {
            return $default;
        }

        $config = require $file;
        if (isset(self::$local[$paths[0]])) {
            $config = array_replace_recursive($config, self::$local[$paths[0]]);
        }

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

    public static function has($paths, $default = null)
    {
        $paths = explode('.', $paths);

        $file = self::$folder . "/" . $paths[0] . ".php";
        if (!file_exists($file)) {
            return false;
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
                return false;
            }
        }
        return true;
    }
}
