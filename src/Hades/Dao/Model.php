<?php

namespace Hades\Dao;

use Hades\Config\Config;

class Model
{
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
            $table = \Hades\Utils\String::fromCamlCase(substr($class, 0, -3));
            $this->dao = new \Hades\Dao\Dao($table);
        }

        return call_user_func_array(array($this, $method), $args);
    }


    private function getTableVars()
    {
        $data = get_object_vars($this);
        unset($data['dao']);
        return $data;
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

    protected function update(array $sets)
    {
        $pdo = $this->dao->getWritePdo();
        $pk = $this->dao->getPk();
        $table = $this->dao->getTable();

        $fields = $replace = $values = [];
        foreach ($sets as $key => $value) {
            $fields[] = $key;
            $replace[] = ":{$key}";
            $values[":{$key}"] = $value;

            $this->$key = $value;
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

        $sql = "DELETE {$table} WHERE {$this->pk} = {$this->$pk}";
        $stm = $pdo->prepare($sql);
        $stm->execute();
        return $this;
    }
}
