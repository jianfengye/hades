<?php

namespace Hades\Dao;

class Register
{
    private static $dao;

    public static function load($container, $dao)
    {
        self::$dao = $dao;

        foreach ($dao as $table => $config) {
            $model_name = self::modelName($table);
            $configConstr = new \Hades\Dao\Config($table, $config);

            $alias_model = '\Hades\Dao\Model';

            if (isset($config['model'])) {
                $alias_model = $config['model'];
            }


            if (!class_exists($model_name, false)) {
                eval("class {$model_name} extends {$alias_model} {} ");
            }

            $container->bind($model_name, $alias_model, [$configConstr]);

            $dao_name = self::daoName($table);
            $alias_dao = '\Hades\Dao\Dao';

            /*
            if (!class_exists($dao_name, false)) {
                eval("class {$dao_name} extends {$alias_dao} {} ");
            }
            */

            $container->bind($dao_name, $alias_dao, [$configConstr]);
        }
    }

    public static  function modelName($table)
    {
        return self::tableToClass($table) . 'Model';
    }

    public static function daoName($table)
    {
        return self::tableToClass($table) . 'Dao';
    }

    public static function config($table)
    {
        return self::$dao[$table];
    }

    // convert table to className
    private static function tableToClass($table)
    {
        $sections = explode('_', $table);
        return implode("", array_map('ucfirst', $sections));
    }
}
