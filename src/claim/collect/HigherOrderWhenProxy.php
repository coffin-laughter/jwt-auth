<?php
/**
 * FileName: HigherOrderWhenProxy.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-23 17:52
 */

namespace coffin\jwtauth\claim\collect;


class HigherOrderWhenProxy
{
    protected $collection;

    protected $condition;

    public function __construct(Enumerable $collection, $condition)
    {
        $this->condition = $condition;
        $this->collection = $collection;
    }

    public function __get($key)
    {
        return $this->condition
            ? $this->collection->{$key}
            : $this->collection;
    }

    public function __call($method, $parameters)
    {
        return $this->condition
            ? $this->collection->{$method}(...$parameters)
            : $this->collection;
    }
}