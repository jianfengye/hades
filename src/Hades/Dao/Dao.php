<?php

namespace Hades\Dao;

use Hades\Facade\Facade;

class Dao
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

    protected function find($id, $columns = [])
    {
        $builder = $this->builder()->where($this->config->pk(), $id)->columns($columns);
        return $builder->slave()->get();
    }

    protected function finds(array $ids, $columns = [])
    {
        $builder = $this->builder()->whereIn($this->config->pk(), $ids)->columns($columns);
        return $builder->slave()->gets();
    }

    protected function get(array $wheres, array $orderBy = [], $columns = [])
    {
        $builder = $this->builder();
        foreach ($wheres as $where) {
            if (!is_array($where)) {
                continue;
            }
            $builder = call_user_func_array([$builder, 'where'], $where);
        }

        foreach ($orderBy as $key => $value) {
            $builder = call_user_func_array([$builder, 'orderBy'], [$key, $value]);
        }
        $builder = $builder->columns($columns)
        return $builder->slave()->get();
    }

    protected function gets(array $wheres = [], array $orderBy = [], $columns = [])
    {
        $builder = $this->builder();
        foreach ($wheres as $where) {
            if (!is_array($where)) {
                continue;
            }
            $builder = call_user_func_array([$builder, 'where'], $where);
        }

        foreach ($orderBy as $key => $value) {
            $builder = call_user_func_array([$builder, 'orderBy'], [$key, $value]);
        }
        $builder = $builder->columns($columns)
        return $builder->slave()->gets();
    }

    protected function delete(array $wheres = array())
    {
        $builder = $this->builder()->action('DELETE');
        foreach ($wheres as $where) {
            if (!is_array($where)) {
                continue;
            }
            $builder = call_user_func_array([$builder, 'where'], $where);
        }

        return $builder->master()->execute();
    }

    protected function num(array $wheres)
    {
        $builder = $this->builder();
        foreach ($wheres as $where) {
            if (!is_array($where)) {
                continue;
            }
            $builder = call_user_func_array([$builder, 'where'], $where);
        }

        return $builder->slave()->count();
    }
}
