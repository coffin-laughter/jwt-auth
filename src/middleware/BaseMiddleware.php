<?php

namespace coffin\jwtauth\middleware;

use think\facade\Config;
use think\facade\Cookie;
use coffin\jwtauth\JWTAuth as Auth;

class BaseMiddleware
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    protected function setAuthentication($response, $token = null)
    {
        $token = $token ?: $this->auth->refresh();
        $this->auth->setToken($token);

        if (in_array('cookie', Config::get('jwt.token_mode'))) {
            Cookie::set('token', $token);
        }

        if (in_array('header', Config::get('jwt.token_mode'))) {
            $response = $response->header(['Authorization' => 'Bearer ' . $token]);
        }

        return $response;
    }
}
