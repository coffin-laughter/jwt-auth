<?php

namespace coffin\jwtauth\provider\storage;

use think\facade\Cache;
use coffin\jwtauth\contract\Storage;

class Tp5 implements Storage
{
    public function delete($key)
    {
        return Cache::rm($key);
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function set($key, $time = 0)
    {
        return Cache::set($key, time(), $time);
    }
}
