<?php


namespace coffin\jwtauth\middleware;

class JWTAuth extends BaseMiddleware
{
    public function handle($request, \Closure $next)
    {
        $this->auth->auth();

        return $next($request);
    }
}
