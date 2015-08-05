<?php

namespace Hades\Container;

// container
class Container
{
    protected function __construct() {}

    private static $instance;

    private $last_alias;

    // class bindings
    private $bindings = [];

    // get global instace
    public static function instance()
    {
        if (null != static::$instance) {
            return static::$instance;
        }

        $container = new static();

        static::$instance = $container;
        return static::$instance;
    }

    // load config
    public function load($config)
    {
        if (isset($config['bind'])) {
            foreach ($config['bind'] as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (isset($config['singleton'])) {
            foreach ($config['singleton'] as $key => $value) {
                $this->singleton($key, $value);
            }
        }
    }

    // bind a class
    // the class will have new instance when use
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

        if (!class_exists($contract, false)) {
            eval("class {$contract} extends \Hades\Container\Facade {} ");
        }
    }

    // bind a class
    // the class will instance one time when use
    public function singleton($contract, $class, $args = [])
    {
        $reflect = new \ReflectionClass($class);
        $instance = $reflect->newInstanceArgs($args);

        $this->bindings[$contract] = [
            'type' => 'singleton',
            'instance' => $instance,
            'class' => $class,
            'args' => $args
        ];

        if (!class_exists($contract, false)) {
            eval("class {$contract} extends \Hades\Container\Facade {} ");
        }
    }

    // make some contract
    public function make($contract)
    {
        if (empty($this->bindings[$contract])) {
            throw new \LogicException('Not found contract:' . $contract);
        }

        $class = $this->bindings[$contract];
        if ($class['type'] == 'closure') {
            return call_user_func($class['closure'], $class['class'], $class['args']);
        } else if ($class['type'] == 'singleton') {
            return $class['instance'];
        }

        throw new \LogicException('Not found contract:' . $contract);
    }

    // check is there exist contract
    public function have($contract)
    {
        return isset($this->bindings[$contract]);
    }

    public function getBinding($contract)
    {
        if ($this->have($contract)) {
            return $this->bindings[$contract];
        }
        return null;
    }
}
