<?php

namespace Hades\Dao;

class DaoHelper
{
    public static function modelName($table)
    {
        $config = \Hades\Config\Config::get('dao');

        if (empty($config[$table])) {
            return '\Hades\Dao\Model';
        }

        $dao = $config[$table];
        if (isset($dao['model'])) {
            return $dao['model'];
        }

        return '\Hades\Dao\Model';
    }
}
