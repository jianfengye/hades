<?php

namespace Hades\Dao;
use Hades\Facade\Facade;

class Model
{
    use Facade;

    private $config;

    private $relations = [];

    public function __construct($config = [])
    {
        if ($config) {
            $this->config = $config;
            return;
        }

        $alias = self::alias();
        $binding = \Hades\Container\Container::instance()->getBinding($alias);
        if (empty($binding)) {
            throw new \LogicException("model {$alias} does not contain in container");
        }
        $this->config = current($binding['args']);
    }

    public function config()
    {
        return $this->config;
    }

    public function builder()
    {
        return new Builder($this->config);
    }

    public function save()
    {
        $pk = $this->config()->pk();
        if (empty($this->$pk)) {
            return $this->insert();
        }
        return $this->update();
    }

    public function insert()
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

    public function update()
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

    public function delete()
    {
        $pk = $this->config->pk();
        if (empty($this->$pk)) {
            throw new \LogicException('Model Primary Key Can Not Be Empty');
        }

        $builder = $this->builder()->master()->action('DELETE');
        $builder->where($pk, $this->$pk);
        $builder->execute();
    }

    public function load($relation)
    {
        return Relation::loadModel($this, $relation);
    }

    public function setRelation($name, $obj)
    {
        $this->relations[$name] = $obj;
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
