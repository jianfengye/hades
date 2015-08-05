<?php

namespace Hades\Container;

use Hades\Container\Container;

// if class use this trait
// if container has this class's alias
// Foo::bar() is avaiable
class Facade
{
    public static function alias()
    {
        return get_called_class();
    }

    public static function __callStatic($method, $args)
    {
        $alias = trim(self::alias(), '\\');
        if (empty($alias)) {
            throw new \LogicException('use Facade must set alias');
        }
        $instance = Container::instance()->make($alias);

        return call_user_func_array(array($instance, $method), $args);
    }

    public function __call($method, $args)
    {
        $alias = trim(self::alias(), '\\');
        if (empty($alias)) {
            throw new \LogicException('use Facade must set alias');
        }
        $instance = Container::instance()->make($alias);
        return call_user_func_array(array($instance, $method), $args);
    }
}
