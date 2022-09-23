<?php


namespace coffin\jwtauth\http\middleware;

use coffin\jwtauth\exception\TokenExpiredException;

class JWTAuthAndRefresh extends BaseMiddleware
{
    public function handle($request, \Closure $next)
    {
        try {
            $this->auth->auth();
        } catch (TokenExpiredException $e) {
            $this->auth->refresh();
            $response = $next($request);

            return $this->setAuthentication($response);
        }

        return $next($request);
    }
}
