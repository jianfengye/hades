<?php

class Foo
{
    public function Bar()
    {
        echo 'Bar';
    }

    private function Bar2()
    {
        echo 'Bar2';
    }

    public static function __callStatic($method, $args)
    {
        $instance = new Foo();
        $reflect = new \ReflectionMethod($instance, $method);

        var_dump($reflect->isPrivate());
        exit;
        return call_user_func_array(array($instance, $method), $args);
    }
}

$foo = new Foo();
Foo::Bar2();
