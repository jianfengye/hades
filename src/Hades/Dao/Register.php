<?php

namespace Hades\Dao;

class Register
{
    public static function load($container, $dao)
    {
        foreach ($dao as $table => $config) {
            $model_name = self::modelName($table);
            $alias_model = '\Hades\Dao\Model';

            if (isset($config['model'])) {
                $alias_model = $config['model'];
            }

            $container->bind($model_name, $alias_model, [$table, $config]);

            $dao_name = self::daoName($table);
            $alias_dao = '\Hades\Dao\Dao';
            $container->bind($dao_name, $alias_dao, [$table, $config]);
        }
    }

    private static  function modelName($table)
    {
        return self::tableToClass($table) . 'Model';
    }

    private static function daoName($table)
    {
        return self::tableToClass($table) . 'Dao';
    }

    // convert table to className
    private static function tableToClass($table)
    {
        $sections = explode('_', $table);
        return implode("", array_map('ucfirst', $sections));
    }
}
