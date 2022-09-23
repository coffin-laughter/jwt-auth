<?php

namespace coffin\jwtauth;

use coffin\jwtauth\provider\ThinkPHP6ServiceProvider;

class Service extends \think\Service
{
    public function boot()
    {
        (new ThinkPHP6ServiceProvider($this->app))->register();
    }
}
