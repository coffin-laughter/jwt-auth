<?php


namespace coffin\jwtauth\contract;

interface Storage
{
    public function set($key, $time = 0);

    public function get($key);

    public function delete($key);
}
