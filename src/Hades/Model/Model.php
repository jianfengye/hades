<?php

namespace Hades\Model;

class Model
{
    // the table map to this Dao
    private $table;

    // table's primary key
    private $pk = 'id';

    // the class map to this Dao
    private $class = 'stdclass';

    // pdo instance
    private $pdo;

    // change connection
    public function connection($connection) 
    {
        $this->pdo = new \PDO("pgsql:dbname={$config['database']}; 
                host={$config['host']}; 
                user={$config['username']};
                port={$config['port']};
                password={$config['password']}");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance($table)
    {
        $dao = new Dao();

        $dao->table = $table;
        $map = Config::get('dao');

        $className = $map[$table]['class'];
        //获取class的最后一个名字
        $className = explode('\\', $className);
        $className = end($className);

        $dao->config = $map[$table];

        $dao->dbAdapter = new DBAdapter($map[$table]['class']);

        if (method_exists($dao->dbAdapter, $obj2class)) {
            $dao->obj2class = $obj2class;
        }
        if (method_exists($dao->dbAdapter, $class2obj)) {
            $dao->class2obj = $class2obj;
        }
        if (method_exists($dao->dbAdapter, $objs2classes)) {
            $dao->objs2classes = $objs2classes;
        }
        if (method_exists($dao->dbAdapter, $classes2objs)) {
            $dao->classes2objs = $classes2objs;
        }

        if (isset($map[$table]['soft_delete'])) {
            $dao->soft_delete = boolval($map[$table]['soft_delete']);
        }

        $dao->pk = $map[$table]['pk'];
        
        $config = Config::get('database.pgsql');
        $dao->pdo = new \PDO("pgsql:dbname={$config['database']}; 
                host={$config['host']}; 
                user={$config['username']};
                port={$config['port']};
                password={$config['password']}");
        $dao->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $dao;
    }

    // 获取pdo
    public function getPdo()
    {
        return $this->pdo;
    }

    // 按照结构插入
    public function insert($obj)
    {
        $obj = call_user_func_array(array($this->dbAdapter,$this->class2obj), [$obj]);

        $data = get_object_vars($obj);
        if ($this->auto_time) {
            $data['created_at'] = time();
        }
        $fields = $replace = $values = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }
            $fields[] = $key;
            $replace[] = ":{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "INSERT INTO {$this->table} (". implode(',', $fields) .") values (". implode(',', $replace) .") ";
        if (!empty($this->config['without_returning'])) {
            $stm = $this->pdo->prepare($sql);
            $stm->execute($values);
            return;
        }
        $sql .= " returning * ";
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);

        $obj = $stm->fetchObject();
        return call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
    }

    // 按照id删除
    public function destory($id)
    {
        $deleted_at = time();
        if ($this->soft_delete) {
            $sql = "UPDATE {$this->table} SET (deleted_at) = ({$deleted_at}) WHERE {$this->pk} = {$id}";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
        } else {
            $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = {$id}";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
        }
    }

    // 按照条件删除
    public function delete($where)
    {
        $values = [];
        $whereArr = [];
        foreach ($where as $key => $value) {
            if ($value === null) {
                continue;
            }

            $whereArr[] = "{$key} = :{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "";
        if ($whereArr) {
            $sql = " WHERE " . implode(' AND ', $whereArr);
        }

        $deleted_at = time();
        if ($this->soft_delete) {
            $sql = "UPDATE {$this->table} SET (deleted_at) = ({$deleted_at}) " . $sql;
            $stm = $this->pdo->prepare($sql);
            $stm->execute($values);
        } else {
            $sql = "DELETE FROM {$this->table} WHERE {$this->pk} = {$id}" . $sql;
            $stm = $this->pdo->prepare($sql);
            $stm->execute($values);
        }
    }

    // 按照结构更新,返回class
    public function update($obj)
    {
        $obj = call_user_func_array(array($this->dbAdapter,$this->class2obj), [$obj]);

        $pk = $this->pk;
        if (empty($obj->$pk)) {
            throw new \Exception("DBDao error: obj has not set pk");
        }

        $data = get_object_vars($obj);
        if ($this->auto_time) {
            $data['updated_at'] = time();
        }

        $fields = $replace = $values = [];
        $pkValue = 0;
        foreach ($data as $key => $value) {
            if ($key == $this->pk) {
                $pkValue = $value;
                continue;
            }
            if ($value === null) {
                continue;
            }
            $fields[] = $key;
            $replace[] = ":{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "UPDATE {$this->table} SET (". implode(',', $fields) .") = (". implode(',', $replace) .") WHERE {$this->pk} = {$pkValue} returning *";
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
        $obj = $stm->fetchObject();
        return call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
    }
    
    // 按照where进行更新
    public function updateWhere($sets, $where)
    {
        $fields = [];
        $replace = [];
        $values = [];
        $updated_at = time();
        if ($this->auto_time) {
            $data['updated_at'] = $updated_at;
        }
        foreach ($sets as $key => $value) {
            if ($key == $this->pk) {
                $pkValue = $value;
                continue;
            }
            if ($value === null) {
                continue;
            }
            $fields[] = $key;
            $replace[] = "?";
            $values[] = $value;
        }

        $whereFields = [];
        foreach ($where as $key => $value) {
            if ($key == $this->pk) {
                $pkValue = $value;
                continue;
            }
            if ($value === null) {
                continue;
            }

            $whereFields[] = "{$key} = ?";
            $values[] = $value;
        }

        $sql = "UPDATE {$this->table} SET (". implode(',', $fields) .") = (". implode(',', $replace) .") ";
        if ($whereFields) {
            $sql .= " WHERE " . implode(' AND ', $whereFields);
        }
        if ($this->soft_delete) {
            $sql .= " AND deleted_at = 0 ";
        }

        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
    }
    
    // 按照主键查找
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->pk} = ?";
        $stm = $this->pdo->prepare($sql);
        $stm->execute([$id]);
        $obj = $stm->fetchObject();
        return call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
    }

    // 按照多个主键查找, 返回以ids为key的数组，并且按照ids顺序返回
    public function finds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        $sql = "SELECT * FROM {$this->table} where {$this->pk} in (". implode(',', $ids) .")";
        if ($this->soft_delete) {
            $sql .= " AND deleted_at = 0";
        }
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $stm->setFetchMode(\PDO::FETCH_CLASS, 'stdclass');
        $objs = $stm->fetchAll();

        $tmp = [];
        if ($this->objs2classes) {
            $tmp = call_user_func_array(array($this->dbAdapter,$this->objs2classes), [$objs]);
        } else {
            foreach ($objs as $obj) {
                $tmp[] = call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
            }
        }

        $tmp = DBAdapter::objs_key($tmp, $this->pk);

        $ret = [];
        foreach ($ids as $id) {
            if (isset($tmp[$id])) {
                $ret[$id] = $tmp[$id];
            }
        }

        return $ret;
    }

    // 按照查询语句查找单个
    public function get($where, $sqlAppend = "")
    {
        $values = [];
        $whereArr = [];
        foreach ($where as $key => $value) {
            if ($value === null) {
                continue;
            }

            $whereArr[] = "{$key} = :{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "SELECT * FROM {$this->table} ";
        if ($whereArr) {
            $sql .= " WHERE " . implode(' AND ', $whereArr);
        }
        if ($this->soft_delete) {
            $sql .= " AND deleted_at = 0 ";
        }
        if ($sqlAppend) {
            $sql .= $sqlAppend;
        }
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
        $obj = $stm->fetchObject();
        return call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
    }

    // 按照查询语句查找多个
    public function gets($where, $sqlAppend = "")
    {
        $values = [];
        $whereArr = [];
        foreach ($where as $key => $value) {
            if ($value === null) {
                continue;
            }

            $whereArr[] = "{$key} = :{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "SELECT * FROM {$this->table} ";
        if ($whereArr) {
            $sql .= " WHERE " . implode(' AND ', $whereArr);
        }
        if ($this->soft_delete) {
            $sql .= " AND deleted_at = 0 ";
        }
        if ($sqlAppend) {
            $sql .= $sqlAppend;
        }
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
        $stm->setFetchMode(\PDO::FETCH_CLASS, 'stdclass');
        $objs = $stm->fetchAll();

        if ($this->objs2classes) {
            return call_user_func_array(array($this->dbAdapter,$this->objs2classes), [$objs]);
        }

        $ret = [];
        foreach ($objs as $obj) {
            $ret[] = call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
        }
        return $ret;
    }

    // 查询个数
    public function num($where, $sqlAppend = "")
    {
        $values = [];
        $whereArr = [];
        foreach ($where as $key => $value) {
            if ($value === null) {
                continue;
            }

            $whereArr[] = "{$key} = :{$key}";
            $values[":{$key}"] = $value;
        }

        $sql = "SELECT count(*) as num FROM {$this->table} ";
        if ($whereArr) {
            $sql .= " WHERE " . implode(' and ', $whereArr);
        }
        if ($this->soft_delete) {
            $sql .= " AND deleted_at = 0 ";
        }
        if ($sqlAppend) {
            $sql .= $sqlAppend;
        }
        $stm = $this->pdo->prepare($sql);
        $stm->execute($values);
        $stm->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stm->fetch();
        if (empty($result['num'])) {
            return 0;
        }
        return $result['num'];
    }

    // 增长
    public function increase($id, $field, $step = 1)
    {
        $sql = "UPDATE {$this->table} SET ({$field}) = ({$field}+1) WHERE {$this->pk} = {$id} returning *";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        $obj = $stm->fetchObject();
        return call_user_func_array(array($this->dbAdapter,$this->obj2class), [$obj]);
    }
}