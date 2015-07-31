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

    private $sets;

    private $returning;

    public function __constrct($config)
    {
        $this->config = $config;
    }

    public function action($action = 'SELECT')
    {
        $this->action = $action;
    }

    public function set($column, $value)
    {
        $this->sets[$column] = $value;
    }

    public function connection()
    {
        return $this->connection;
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

    public function returning(array $columns = [])
    {
        $this->returning = $columns;
    }

    public function get()
    {
        list($sql, $values) = $this->prepare();
        $pdo = $this->connection->pdo()->prepare($sql);
        $stm->execute($values);
        return $stm->fetchObject(Register::modelName($this->table));
    }

    public function gets()
    {
        list($sql, $values) = $this->prepare();
        $pdo = $this->connection->pdo()->prepare($sql);
        $stm->setFetchMode(\PDO::FETCH_CLASS, Register::modelName($this->table));
        $stm->execute($values);
        $objs = $stm->fetchAll();
        return new Collections($objs);
    }

    public function count()
    {
        $this->columns(['count(1)'])
        $data = $this->get();
        return intval($data['count(1)']);
    }

    public function lastInsertId()
    {
        return $this->connection->pdo()->lastInsertId();
    }

    public function execute()
    {
        list($sql, $values) = $this->prepare();
        $pdo = $this->connection->pdo()->prepare($sql);
        $stm->execute($values);
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
        $values = [];

        if ($this->action == 'UPDATE') {
            $sql = "{$this->action} " . $this->config->table() ;
        } else {
            $sql = "{$this->action} " . implode(',', $this->columns) . " FROM " . $this->config->table();
        }

        if ($this->sets) {
            $sql .= " SET ";
            foreach ($this->sets as $key => $value) {
                $sql .= " {$key} = {$value}";
            }
        }


        if ($this->wheres) {
            $sql .= " WHERE ";
            foreach ($this->wheres as $index => $where) {
                if ($index != 0) {
                    $sql .= " " . $where['append'];
                }

                $sql .= " {$where['column']} {$where['operator']} "

                if (in_array($where['operator'], ['in', 'not in'])) {
                    $sql .= ' (' implode(',', array_fill(0, count($where['value']) -1, '?')) . ' )';
                    $values = array_merge($values, $where['value']);
                } else {
                    $sql .= ' ?';
                    $values = array_merge($values, [ $where['value'] ]);
                }
            }
        }

        if ($this->orders) {
            $sql .= ' ORDER BY ';
            foreach ($this->orders as $order) {
                $sql .= " {$order['column']} {$order['directory']},";
            }
            $sql .= trim($sql, ',');
        }

        if ($this->returing) {
            $sql .= ' RETURNING ' . implode(',', $this->returing);
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset} ";
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit} ";
        }

        return array_values(compact('sql', 'values'));
    }


}
