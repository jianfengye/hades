<?php

namespace Hades\Container;

// container
class Container
{
    // class bindings
    private $bindings = [];


    public function bind($contract, $class)
    {
        $cluser = function() {
            return new $class;
        };
        $this->bindings[$contract] = $cluser;
    }

    public function singleton($contract, $class)
    {
        $instance = new $class;
        $this->bindings[$contract] = $instance;        
    }

    public function make($contract)
    {
        if (empty($this->bindings[$contract])) {
            throw new \Exception("class not bind");
        }

        $class = $this->bindings[$contract];
        if ($class instanceof Closure) {
            return call_user_func($class);
        }

        return $class;
    }
}