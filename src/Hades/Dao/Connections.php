<?php

namespace Hades\Dao;

use Hades\Config\Config;

trait Connections
{
    public function getWritePdo()
    {
        if (!empty($this->writePdo)) {
            return $this->writePdo;
        }

        $config = $this->getWriteConfig();
        $driver = $config['driver'];
        $pdo = new \PDO("{$driver}:dbname={$config['database']};
            host={$config['hostname']};
            port={$config['port']}", $config['username'], $config['password']);
        $this->writePdo = $pdo;
        $this->writeConfig = $config;
        return $this->writePdo;
    }

    public function getReadPdo()
    {
        if (!empty($this->readPdo)) {
            return $this->readPdo;
        }

        $config = $this->getReadConfig();
        $driver = $config['driver'];
        $pdo = new \PDO("{$driver}:dbname={$config['database']};
            host={$config['hostname']};
            port={$config['port']}", $config['username'], $config['password']);
        $this->readPdo = $pdo;
        $this->readConfig = $config;
        return $this->readPdo;
    }

    public function getWriteConfig()
    {
        if (!empty($this->writeConfig)) {
            return $this->writeConfig;
        }

        $masters = Config::get('database.connection.master');
        $databases = Config::get('database.database', 22);
        $index = rand(0, count($masters) - 1);
        $config = $databases[$masters[$index]];
        $this->writeConfig = $config;
        return $config;
    }

    public function getReadConfig()
    {
        if (!empty($this->readConfig)) {
            return $this->readConfig;
        }

        $slaves = Config::get('database.connection.slave');
        $databases = Config::get('database.database');

        $index = rand(0, count($slaves) - 1);
        $config = $databases[$slaves[$index]];
        $this->readConfig = $config;
        return $config;
    }

    public function getWriteDriver()
    {
        $config = $this->getWriteConfig();
        return $config['driver'];
    }

    public function getReaderDriver()
    {
        $config = $this->getReadConfig();
        return $config['driver'];
    }
}
