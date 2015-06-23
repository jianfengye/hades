<?php

abstract class Facade
{
    abstract protected static function getAlias();

    public static function __callStatic($method, $args)
    {
        global $container;

        $alias = self::getAlias();
        $instance = $container->make($alias);

        call_user_func_array(array($instance, $method), $args);
    }
}