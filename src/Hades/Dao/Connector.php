<?php

namespace Hades\Dao;

use Hades\Config\Config;
use Hades\Trait\Singleton;

class Connector
{
    use Singleton;

    private $connnections = [];

    private $masters = [];

    private $slaves = [];

    public static function instance()
    {
        if (null != static::$instance) {
            return static::$instance;
        }

        $instance = new static();

        // parse config
        $databases = Config::get('database.database');
        foreach ($databases as $name => $database) {
            $instance->connnections[] = new Connection($name, $database);
        }

        $masters = Config::get('database.connection.master');
        foreach ($master as $master) {
            $master_config = Config::get("database.database.{$master}");
            if (!empty($master_config)) {
                $instance->masters = new Connection($master, $master_config);
            }
        }
        $slaves = Config::get('database.connection.slave');
        foreach ($slaves as $slave) {
            $slave_config = Config::get("database.database.{$slave}");
            if (!empty($slave_config)) {
                $instance->slaves = new Connection($slave, $slave_config);
            }
        }

        static::$instance = $container;
        return static::$instance;
    }

    // get a slave connection
    public function slave()
    {
        $index = rand(0, count($this->slaves) - 1);
        return $this->slaves[$index];
    }

    // get a master connection
    public function master()
    {
        $index = rand(0, count($this->masters) - 1);
        return $this->masters[$index];
    }
}
