<?php

namespace coffin\jwtauth\provider\storage;

use think\facade\Cache;
use coffin\jwtauth\contract\provider\Storage;

class Tp6 implements Storage
{
    public function delete($key)
    {
        return Cache::delete($key);
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
