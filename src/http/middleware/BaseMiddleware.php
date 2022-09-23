<?php

namespace coffin\jwtauth\http\middleware;

use Cassandra\Exception\UnauthorizedException;
use coffin\jwtauth\exception\TokenInvalidException;
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

    public function checkForToken(Request $request)
    {
        if ( ! $this->auth->parser()->setRequest($request)->hasToken()) {
            throw new TokenInvalidException('oken not provided');
        }
    }

    public function authenticate(Request $request)
    {
        $this->checkForToken($request);

        try {
            if ( ! $this->auth->parseToken()->authenticate()) {
                throw new TokenInvalidException('jwt-auth User not found');
            }
        } catch (JWTException $e) {
            throw new TokenInvalidException('jwt-auth' . $e->getMessage());
        }
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
