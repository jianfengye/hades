<?php

namespace Hades\Config;

Class Config
{
    public static function get($path)
    {
        $config = self::$config;
        $paths = explode('.', $path);
        $value = $config;
        foreach ($paths as $path) {
            if (isset($value[$path])) {
                $value = $value[$path];
            }
        }
        return $value;
    }
}