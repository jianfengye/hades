<?php

namespace Hades\Dao;

use Hades\Config\Config;

class Model
{
    protected $relations;

    public static function modelAlias($class, $model = '')
    {
        if (empty($model)) {
            $model = '\Hades\Dao\Model';
        }
        eval("class {$class} extends {$model} {}");
    }

    private $dao;

    public function __call($method, $args)
    {
        if (empty($this->dao)) {
            $class = get_called_class();
            $table = \Hades\Utils\String::fromCamlCase(substr($class, 0, -5));
            $this->dao = new \Hades\Dao\Dao($table);
        }

        return call_user_func_array(array($this, $method), $args);
    }


    private function getTableVars()
    {
        return call_user_func('get_object_vars', $this);
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
        $pdo = $this->dao->getWritePdo();
        $fields = $replace = $values = [];
        foreach ($this->getTableVars() as $key => $value) {
            $fields[] = $key;
            $replace[] = ":{$key}";
            $values[":{$key}"] = $value;
        }

        $pk = $this->dao->getPk();
        $table = $this->dao->getTable();

        if ($this->dao->getWriteDriver() == 'pgsql') {
            $sql = "INSERT INTO {$table} (". implode(',', $fields) .") values (". implode(',', $replace) .") returning {$pk}";
            $stm = $pdo->prepare($sql);
            $stm->execute($values);
            $obj = $stm->fetchObject();
            $this->$pk = $obj->$pk;
        } else {
            $sql = "INSERT INTO {$table} (". implode(',', $fields) .") values (". implode(',', $replace) .")";
            $stm = $pdo->prepare($sql);
            $stm->execute($values);

            $insert_id = $pdo->lastInsertId();
            $this->$pk = $insert_id;
        }

        return $this;
    }

    protected function update()
    {
        $pdo = $this->dao->getWritePdo();
        $pk = $this->dao->getPk();
        $table = $this->dao->getTable();

        $fields = $replace = $values = [];
        foreach ($this->getTableVars() as $key => $value) {
            $fields[] = $key;
            $replace[] = ":{$key}";
            $values[":{$key}"] = $value;
        }

        $pk = $this->dao->getPk();

        $sql = "UPDATE {$table} SET (". implode(',', $fields) .") = (". implode(',', $replace) .") where {$pk} = {$this->$pk}";
        $stm = $pdo->prepare($sql);
        $stm->execute($values);
        return $this;
    }

    protected function delete()
    {
        $pdo = $this->dao->getWritePdo();
        $pk = $this->dao->getPk();
        $table = $this->dao->getTable();

        $sql = "DELETE FROM {$table} WHERE {$pk} = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$this->$pk]);
        return $this;
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
        if (in_array($name, $this->getTableVars())) {
            return $this->$name;
        }

        if (isset($this->relations[$name])) {
            return $this->relations[$name];
        }
    }
}
