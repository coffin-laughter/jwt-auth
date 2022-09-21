<?php

namespace coffin\jwtauth;

use coffin\jwtauth\middleware\InjectJwt;
use coffin\jwtauth\command\SecretCommand;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(SecretCommand::class);
        $this->app->middleware->add(InjectJwt::class);
    }
}
