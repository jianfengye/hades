<?php

namespace Hades\Dao;


class Connection
{
    private $name;

    private $driver;

    private $hostname;

    private $port;

    private $password;

    private $username;

    private $database;

    private $pdo;

    public function __construct($name, $config)
    {
        $this->name = $name;
        if (!empty($config['driver'])) {
            $this->driver = $config['driver'];
        }
        if (!empty($config['hostname'])) {
            $this->hostname = $config['hostname'];
        }
        if (!empty($config['port'])) {
            $this->port = $config['port'];
        }
        if (!empty($config['password'])) {
            $this->password = $config['password'];
        }
        if (!empty($config['username'])) {
            $this->username = $config['username'];
        }
        if (!empty($config['database'])) {
            $this->database = $config['database'];
        }
    }

    // get raw pdo
    public function pdo()
    {
        if (!empty($this->pdo)) {
            return $this->pdo;
        }

        $dns = "{$this->driver}:";
        if (!empty($this->database)) {
            $dns .= "dbname={$this->database};";
        }
        if (!empty($this->hostname)) {
            $dns .= "host={$this->hostname};";
        }
        if (!empty($this->port)) {
            $dns .= "port={$this->port};";
        }

        $this->pdo = new \PDO($dns, $this->username, $this->password);
        return $this->pdo;
    }

    public function reconnect()
    {
        $this->pdo = null;
        $this->pdo();
        return $this;
    }


    public function driver()
    {
        return $this->driver;
    }
}
