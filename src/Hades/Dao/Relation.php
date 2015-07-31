<?php namespace Hades\Dao;

class Relation
{
    public static function loadModel($model, $name)
    {
        if (empty($model->config()->relation($name))) {
            return $model;
        }

        $relation = $model->config()->relation($name);
        $table = $relation['table'];
        $config = Register::config($table);
        $type = $relation['type'];
        if (empty($table) || empty($config) || empty($type)) {
            return $model;
        }

        $builder = new Builder(new Config($table, $config));
        $key = $config['key'];
        $relate_key = $config['relate_key'];
        switch ($type) {
            case 'has_many':
                $builder->where($relate_key, $model->$key);
                $model->$name = $builder->gets();
                return $model;
            case 'has_one':
                $builder->where($relate_key, $model->$key);
                $model->$name = $builder->get();
                return $model;
            case 'belong_to':
                $builder->where($relate_key, $model->$key);
                $model->$name = $builder->get();
                return $model;
            default:
                return $model;
        }
        return $model;
    }

    public static function loadCollection($collection, $relation)
    {
        if (empty($collection->config()->relation($name))) {
            return $collection;
        }

        $relation = $collection->config()->relation($name);
        $table = $relation['table'];
        $config = Register::config($table);
        $type = $relation['type'];
        if (empty($table) || empty($config) || empty($type)) {
            return $collection;
        }

        $builder = new Builder(new Config($table, $config));
        $key = $config['key'];
        $relate_key = $config['relate_key'];
        $keys = [];
        foreach ($collection as $model) {
            $keys[] = $model->$key;
        }

        switch ($type) {
            case 'has_many':
                $relateModels = $builder->whereIn($relate_key, $keys)->gets();
                foreach ($collection as $model) {
                    $my_key = $model->$key;
                    $tmpModels = [];
                    foreach ($relateModels as $relateModel) {
                        if ($my_key == $relateModel->$relate_key){
                            $tmpModels[] = $relateModel;
                        }
                    }
                    $model->$name = new Collection($tmpModels);
                }
                return $collection;
            case 'has_one':
                $relateModels = $builder->whereIn($relate_key, $keys)->gets();
                foreach ($collection as $model) {
                    $my_key = $model->$key;
                    foreach ($relateModels as $relateModel) {
                        if ($my_key == $relateModel->$relate_key){
                            $model->$name = $relateModel;
                            break;
                        }
                    }
                }
                return $collection;
            case 'belong_to':
                $relateModels = $builder->whereIn($relate_key, $keys)->gets();
                foreach ($collection as $model) {
                    $my_key = $model->$key;
                    foreach ($relateModels as $relateModel) {
                        if ($my_key == $relateModel->$relate_key){
                            $model->$name = $relateModel;
                            break;
                        }
                    }
                }
                return $collection;
            default:
                return $collection;
        }

        return $colleciton;
    }
}
