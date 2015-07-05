<?php

namespace Hades\Dao;

use Hades\Config\Config;

class Dao
{
    use Connections;

    public static function daoAlias($class)
    {
        eval("class {$class} extends \Hades\Dao\Dao {}");
    }

    public static function __callStatic($method, $args)
    {
        $class = get_called_class();
        $table = \Hades\Utils\String::fromCamlCase(substr($class, 0, -3));
        $instance = new $class($table);
        return call_user_func_array(array($instance, $method), $args);
    }


    private $table;
    private $pk;
    private $modelName;

    public function __constrct($table)
    {
        $this->table = $table;
        $this->pk = Config::get("dao.{$this->table}.pk");
        $this->modelName = \Hades\Utils\String::modelNameByTable($this->table);
    }

    public function getPk()
    {
        return $this->pk;
    }

    public function getTable()
    {
        return $this->table;
    }

    protected function find($id)
    {
        $pdo = $this->getReadPdo();

        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id]);
        return $stm->fetchObject($this->modelName);
    }

    protected function finds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} in (". implode(',', $ids) .")";
        $stm = $this->getReadPdo()->prepare($sql);
        $stm->execute();
        $stm->setFetchMode(\PDO::FETCH_CLASS, $this->modelName);
        $objs = $stm->fetchAll();
        return new Collections($objs);
    }

    protected function get(array $conds, array $orderBy = [])
    {
        $values = $whereArr = [];
        foreach ($conds as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $whereArr[] = "{$key} {$value[0]} :{$key}";
                $values[":{$key}"] = $value[1];
            } else {
                $whereArr[] = "{$key} = :{$key}";
                $values[":{$key}"] = $value;
            }
        }

        $sql = "SELECT * FROM {$this->table} ";
        if ($whereArr) {
            $sql .= " WHERE " . implode(' AND ', $whereArr);
        }

        if ($orderBy) {
            $sql .= " order by ";
            foreach ($orderby as $key => $rank) {
                $sql .= $key . ' ' . $rank;
                $sql .= ',';
            }
            $sql = trim(',', $sql);
        }

        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
        return $stm->fetchObject($this->modelName);
    }

    protected function gets(array $conds, array $orderBy = [])
    {
        $values = $whereArr = [];
        foreach ($conds as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $whereArr[] = "{$key} {$value[0]} :{$key}";
                $values[":{$key}"] = $value[1];
            } else {
                $whereArr[] = "{$key} = :{$key}";
                $values[":{$key}"] = $value;
            }
        }

        $sql = "SELECT * FROM {$this->table} ";
        if ($whereArr) {
            $sql .= " WHERE " . implode(' AND ', $whereArr);
        }

        if ($orderBy) {
            $sql .= " order by ";
            foreach ($orderby as $key => $rank) {
                $sql .= $key . ' ' . $rank;
                $sql .= ',';
            }
            $sql = trim(',', $sql);
        }

        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $stm->setFetchMode(\PDO::FETCH_CLASS, $this->modelName);
        $objs = $stm->fetchAll();
        return new Collections($objs);
    }

    protected function num(array $where)
    {

    }
}
