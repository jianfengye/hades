<?php

namespace Hades\Dao;

abstrct class Facade
{
    private static $table = '';

    public static function setTable($table)
    {
        self::$table = $table;
    }

    public static function getTable()
    {
        return self::$table;
    }
}
