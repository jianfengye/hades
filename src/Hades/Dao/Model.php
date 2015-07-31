<?php

namespace Hades\Dao;

use Hades\Config\Config;

class Model
{
    use Facade;

    private $config;

    public function __construct($table, $config)
    {
        $this->config = new Config($table, $config);
    }

    private function config()
    {
        return $this->config;
    }

    protected function builder()
    {
        return new Builder($this->config);
    }

    protected function save()
    {
        $pk = $this->dao->getPk();
        if (empty($this->$pk)) {
            return $this->insert();
        }
        return $this->update();
    }

    protected function insert()
    {
        $fields = call_user_func('get_object_vars', $this);
        $builder = $this->builder()->master()->action('INSERT');
        foreach ($fields as $key => $value) {
            $builder->set($key, $value);
        }

        $pk = $this->config->pk();
        if ($builder->connection()->driver() == 'pgsql') {
            $builder->returning([$pk]);
            $obj = $builder->get();
            $this->$pk = $obj->$pk;
            return $this;
        }

        $builder->execute();
        $this->$pk = $builder->lastInsertId();
        return $this;
    }

    protected function update()
    {
        $fields = call_user_func('get_object_vars', $this);
        $pk = $this->config->pk();

        if (empty($this->$pk)) {
            throw new \LogicException('Model Primary Key Can Not Be Empty');
        }

        $builder = $this->builder()->master()->action('UPDATE');
        foreach ($fields as $key => $value) {
            if ($key != $pk) {
                $builder->set($key, $value);
            }
        }
        $builder->where($pk, $this->$pk);
        $builder->execute();
        return $this;
    }

    protected function delete()
    {
        $pk = $this->config->pk();
        if (empty($this->$pk)) {
            throw new \LogicException('Model Primary Key Can Not Be Empty');
        }

        $builder = $this->builder()->master()->action('DELETE');
        $builder->where($pk, $this->$pk);
        $builder->execute();
    }

    public function load(string $relation)
    {
        $config = $this->dao->getConfig();
        if (!isset($config['relations'])) {
            return $this;
        }

        if (!isset($config['relations'][$relation])) {
            return $this;
        }

        return Relation::loadModel($this, $config['relations'][$relation]);
    }

    public function __get($name)
    {
        if (in_array($name, call_user_func('get_object_vars', $this))) {
            return $this->$name;
        }

        if (isset($this->relations[$name])) {
            return $this->relations[$name];
        }
    }
}
