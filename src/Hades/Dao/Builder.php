<?php

namespace Hades\Dao;

class Builder
{
    private $config;

    private $action = 'SELECT';

    private $wheres;

    private $columns;

    private $orders;

    private $offset;

    private $limit;

    private $connection;

    private $sets;

    private $returning;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function action($action = 'SELECT')
    {
        $this->action = $action;
        return $this;
    }

    public function set($column, $value)
    {
        $this->sets[$column] = $value;
        return $this;
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
        ];
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
        ];
        return $this;
    }

    public function whereIn($column, $values)
    {
        $this->wheres[] = [
            'append' => 'and',
            'column' => $column,
            'operator' => 'in',
            'value' => $values,
        ];
        return $this;
    }

    public function whereNotIn($column, $values)
    {
        $this->wheres[] = [
            'append' => 'and',
            'column' => $column,
            'operator' => 'not in',
            'value' => $values,
        ];
        return $this;
    }

    public function orderBy($column, $directory = 'asc')
    {
        $this->orders[] = [
            'column' => $column,
            'directory' => $directory,
        ];
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function columns(array $columns = [])
    {
        $this->columns = $columns;
        return $this;
    }

    public function returning(array $columns = [])
    {
        $this->returning = $columns;
        return $this;
    }

    public function get()
    {
        list($sql, $values) = $this->prepare();
        \Hades\Log\Logger::instance('query')->info('Builder::get',[compact('sql', 'value')]);
        $stm = $this->connection()->pdo()->prepare($sql);
        $stm->execute($values);
        return $stm->fetchObject(Register::modelName($this->config->table()), [$this->config]);
    }

    public function gets()
    {
        list($sql, $values) = $this->prepare();
        \Hades\Log\Logger::instance('query')->info('Builder::gets', [compact('sql', 'value')]);
        $stm = $this->connection()->pdo()->prepare($sql);
        $stm->setFetchMode(\PDO::FETCH_CLASS, Register::modelName($this->config->table()), [$this->config]);
        $stm->execute($values);
        $objs = $stm->fetchAll();
        return new Collection($objs);
    }

    public function count()
    {
        $this->columns(['count(1) as count']);
        $data = $this->get();
        return $data->count;
    }

    public function lastInsertId()
    {
        return $this->connection()->pdo()->lastInsertId();
    }

    public function execute()
    {
        list($sql, $values) = $this->prepare();
        \Hades\Log\Logger::instance('query')->info('Builder::execute', [compact('sql', 'value')]);
        $stm = $this->connection()->pdo()->prepare($sql);
        $stm->execute($values);
    }

    public function connection()
    {
        if (!empty($this->connection)) {
            return $this->connection;
        }

        if (strtoupper($this->action) == 'SELECT') {
            $this->slave();
        }
        $this->master();
        return $this->connection;
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

        if (empty($this->columns)) {
            $this->columns = [ '*' ];
        }

        if (strtoupper($this->action) == 'UPDATE') {
            $sql = "{$this->action} " . $this->config->table() ;
        } else if (strtoupper($this->action) == 'INSERT') {
            $sql = "{$this->action} INTO " . $this->config->table() ;
        } else if (strtoupper($this->action) == 'DELETE') {
            $sql = "{$this->action} FROM " . $this->config->table();
        } else {
            $sql = "{$this->action} " . implode(',', $this->columns) . " FROM " . $this->config->table();
        }

        if ($this->sets) {
            $sql .= " SET ";
            foreach ($this->sets as $key => $value) {
                $sql .= " {$key} = ?,";
                $values = array_merge($values, [$value]);
            }
            $sql = trim($sql, ',');
        }


        if ($this->wheres) {
            $sql .= " WHERE ";
            foreach ($this->wheres as $index => $where) {
                if ($index != 0) {
                    $sql .= " " . $where['append'];
                }

                $sql .= " {$where['column']} {$where['operator']} ";

                if (in_array($where['operator'], ['in', 'not in'])) {
                    $sql .= ' (' . implode(',', array_fill(0, count($where['value']), '?')) . ' )';
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
            $sql = trim($sql, ',');
        }

        if ($this->returning) {
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
