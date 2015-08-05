<?php

namespace Hades\Dao;

use Hades\Facade\Facade;

class Dao
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function config()
    {
        return $this->config;
    }

    public function builder()
    {
        return new Builder($this->config);
    }

    public function find($id, $columns = [])
    {
        $builder = $this->builder()->where($this->config->pk(), $id)->columns($columns);
        return $builder->get();
    }

    public function finds(array $ids, $columns = [])
    {
        $builder = $this->builder()->whereIn($this->config->pk(), $ids)->columns($columns);
        return $builder->gets();
    }

    public function get(array $wheres, array $orderBy = [], $columns = [])
    {
        $builder = $this->builder();
        foreach ($wheres as $key => $where) {
            $args = [$key];
            if (!is_array($where)) {
                $args = array_merge($args, ['=' , $where]);
            } else {
                $args = array_merge($args, $where);
            }
            $builder = call_user_func_array([$builder, 'where'], $args);
        }

        foreach ($orderBy as $key => $value) {
            $builder = call_user_func_array([$builder, 'orderBy'], [$key, $value]);
        }
        $builder = $builder->columns($columns);
        return $builder->get();
    }

    public function gets(array $wheres = [], array $orderBy = [], $columns = [])
    {
        $builder = $this->builder();
        foreach ($wheres as $key => $where) {
            $args = [$key];
            if (!is_array($where)) {
                $args = array_merge($args, ['=' , $where]);
            } else {
                $args = array_merge($args, $where);
            }
            $builder = call_user_func_array([$builder, 'where'], $args);
        }

        foreach ($orderBy as $key => $value) {
            $builder = call_user_func_array([$builder, 'orderBy'], [$key, $value]);
        }
        $builder = $builder->columns($columns);
        return $builder->gets();
    }

    public function delete(array $wheres = array())
    {
        $builder = $this->builder()->action('DELETE');
        foreach ($wheres as $key => $where) {
            $args = [$key];
            if (!is_array($where)) {
                $args = array_merge($args, ['=' , $where]);
            } else {
                $args = array_merge($args, $where);
            }
            $builder = call_user_func_array([$builder, 'where'], $args);
        }

        return $builder->execute();
    }

    public function num(array $wheres)
    {
        $builder = $this->builder();
        foreach ($wheres as $key => $where) {
            $args = [$key];
            if (!is_array($where)) {
                $args = array_merge($args, ['=' , $where]);
            } else {
                $args = array_merge($args, $where);
            }
            $builder = call_user_func_array([$builder, 'where'], $args);
        }

        return $builder->count();
    }
}
