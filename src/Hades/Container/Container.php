<?php

namespace Hades\Container;

// container
class Container
{
    // class bindings
    private $bindings = [];


    public function bind($contract, $class, $args = [])
    {
        $closure = function($class, $args) {
            $reflect = new \ReflectionClass($class);
            return $reflect->newInstanceArgs($args);
        };

        $this->bindings[$contract] = [
            'type' => 'closure',
            'closure' => $closure,
            'class' => $class,
            'args' => $args
        ];
    }

    public function singleton($contract, $class, $args = [])
    {
        $reflect = new \ReflectionClass($class);
        $instance = $reflect->newInstanceArgs($args);

        $this->bindings[$contract] = [
            'type' => 'singleton',
            'instance' => $instance,
            'args' => $args
        ];      
    }

    public function make($contract)
    {
        if (empty($this->bindings[$contract])) {
            throw new \Exception("class not in container");
        }

        $class = $this->bindings[$contract];
        if ($class['type'] == 'closure') {
            return call_user_func($class['closure'], $class['class'], $class['args']);
        } else if ($class['type'] == 'singleton') {
            return $class['instance'];
        }

        throw new \Exception("class not in container");
    }

    public function have($contract)
    {
        return isset($this->bindings[$contract]);
    }

}