<?php

namespace Hades\Dao;

use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use JsonSerializable;
use IteratorAggregate;

class Collection implements ArrayAccess, IteratorAggregate, Countable, JsonSerializable
{
    private $models = [];
    private $config;

    public function __construct(array $models)
    {
        if (empty($models)) {
            return;
        }
        $this->models = $models;
        $this->config = current($models)->config();
    }

    public function config()
    {
        return $this->config;
    }

    public function load($relation)
    {
        return Relation::loadCollection($this, $relation);
    }

    public function jsonSerialize()
    {
        return json_encode($this->models);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->models[] = $value;
        } else {
            $this->models[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->models[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->models[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->models[$offset]) ? $this->container[$offset] : null;
    }

    public function count()
    {
        return count($this->models);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->models);
    }

    public function lists($field)
    {
        $ret = [];
        foreach($this as $item) {
            $ret[] = $item->$field;
        }
        return $ret;
    }
}
