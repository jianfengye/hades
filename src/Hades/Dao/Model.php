<?php

namespace Hades\Dao;

class Model extends Facade
{
    private $table;

    public function __construct()
    {
        $this->table = self::getTable();
    }

    // create or update
    public function save()
    {

    }

    // update models's fields
    public function update(array $fields)
    {

    }

    public function delete()
    {

    }
}
