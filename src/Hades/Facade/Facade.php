<?php

namespace Hades\Facade;

trait Facade
{
    public static function getAlias()
    {
        return get_called_class();
    }

    public static function __callStatic($method, $args)
    {
        global $container;

        $alias = self::getAlias();
        $instance = $container->make($alias);

        return call_user_func_array(array($instance, $method), $args);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this, $method), $args);
    }
}
