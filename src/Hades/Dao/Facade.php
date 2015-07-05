<?php

namespace Hades\Dao;

trait Facade
{
    public static function setTable($table)
    {
        self::$table = $table;
    }

    public static function getTable()
    {
        return self::$table;
    }
}
