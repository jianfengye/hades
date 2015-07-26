<?php

namespace Hades\Dao;

class Builder
{
    private $table;

    private $action;

    private $wheres;

    constant ACTION_SELECT = 'SELECT';
    constant ACTION_UPDATE = 'UPDATE';
    constant ACTION_DELETE = 'DELETE';
    constant ACTION_INSERT = 'INSERT';

    public function __constrct($table)
    {

    }

    public function action($action = self::ACTION_SELECT)
    {

    }

    public function where($field, $operator = null, $value = null)
    {

    }

    public function whereIn($field, $values)
    {

    }

    public function whereNotIn($field, $values)
    {

    }

    public function orderBy($field, $directory = 'asc')
    {

    }

    public function offset($offset)
    {

    }

    public function limit($limit)
    {

    }

    public function orWhere($field, $operator = null, $value = null)
    {

    }

    public function columns(array $columns = [])
    {

    }

    
}
