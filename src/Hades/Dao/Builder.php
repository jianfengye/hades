<?php

namespace Hades\Dao;

class Builder
{
    private $config;

    private $action;

    private $wheres;

    private $columns;

    private $orders;

    private $offet;

    private $limit;

    private $connection;

    public function __constrct($config)
    {
        $this->config = $config;
    }

    public function action($action = 'SELECT')
    {
        $this->action = $action;
    }

    public function where($column, $operator = null, $value = null)
    {
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'append' => 'and',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ]
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'append' => 'or',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ]
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->wheres[] = [
            'append' => 'and',
            'column' => $column,
            'operator' => 'in',
            'value' => $values,
        ]
        return $this;
    }

    public function whereNotIn($column, $values)
    {
        $this->wheres[] = [
            'append' => 'and',
            'column' => $column,
            'operator' => 'not in',
            'value' => $values,
        ]
        return $this;
    }

    public function orderBy($column, $directory = 'asc')
    {
        $this->orders[] = [
            'column' => $column,
            'directory' => $directory,
        ]
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
    }

    public function columns(array $columns = [])
    {
        $this->columns = $columns;
    }

    public function get()
    {

    }

    public function gets()
    {

    }

    public function slave()
    {
        $this->connection = Connector::instance()->slave();
        return $this;
    }

    public function master()
    {
        $this->connection = Connector::instance()->master();
        return $this;
    }

    public function prepare()
    {
        $sql = "{$this->action} " . implode(',', $this->columns) . " FROM " . $this->config->table();
        if ($this->wheres) {
            $sql .= " WHERE ";
        }
    }

    private function selectSql()
    {
        $sql = "select ";
        $sql .= implode(',', $this->columns);
        $sql .= " from " . $this->config->table();
        if (!empty($this->wheres)) {
            $sql .= " where";
            $values = [];
            foreach ($this->wheres as $index => $where) {
                if ($index =! 0) {
                    $sql .= " " .$where['append'];
                }
                $sql .= " {$where['column']}";
                $sql .= " {$where['operator']}";
                if (in_array($where['operator'], ['in', 'not in'])) {
                    $sql .= ' (';
                    $sql .= implode(',', array_fill(0, count($where['value']) -1, '?'));
                    $sql .= ' )';
                } else {
                    $sql .= ' ?';
                }

                $values = array_merge($values, $where['value']);
            }
        }

        if (!empty($this->orders)) {
            $sql .= 'order by';
            foreach ($this->orders as $order) {

            }
        }
    }


}
