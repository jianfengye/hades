<?php

namespace Hades\Trait;

trait Singleton
{
    protected function __construct() {}

    private static $instance;

    // get global instace
    public static function instance()
    {
        if (null != static::$instance) {
            return static::$instance;
        }

        $instance = new static();

        static::$instance = $instance;
        return static::$instance;
    }
}
