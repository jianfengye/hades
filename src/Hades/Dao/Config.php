<?php

namespace Hades\Dao;

// dao table config
class Config
{
    private $table;

    private $config;

    public function __construct($table, $config)
    {
        $this->table = $table;
        $this->config = $config;
    }

    public function table()
    {
        return $this->table;
    }

    public function pk()
    {
        $pk = 'id';
        if (!empty($this->config['pk'])) {
            $pk = $this->config['pk'];
        }
        return $pk;
    }

    public function model()
    {
        $model = '\Hades\Dao\Model';
        if (!empty($this->config['model'])) {
            $model = $this->config['model'];
        }
        return $model;
    }
}
