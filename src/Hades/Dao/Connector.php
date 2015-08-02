<?php

namespace Hades\Dao;

use Hades\Config\Config as HadeConfig;
use Hades\Support\Singleton;

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
        $databases = HadeConfig::get('database.database');
        foreach ($databases as $name => $database) {
            $instance->connnections[] = new Connection($name, $database);
        }

        $masters = HadeConfig::get('database.connection.master');
        foreach ($masters as $master) {
            $master_config = HadeConfig::get("database.database.{$master}");
            if (!empty($master_config)) {
                $instance->masters[] = new Connection($master, $master_config);
            }
        }
        $slaves = HadeConfig::get('database.connection.slave');
        foreach ($slaves as $slave) {
            $slave_config = HadeConfig::get("database.database.{$slave}");
            if (!empty($slave_config)) {
                $instance->slaves[] = new Connection($slave, $slave_config);
            }
        }

        static::$instance = $instance;
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
