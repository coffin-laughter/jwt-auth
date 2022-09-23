<?php


namespace coffin\jwtauth\http\middleware;

use think\Request;
use coffin\jwtauth\provider\JWT as JWTProvider;

class InjectJwt
{
    public function handle(Request $request, $next)
    {
        (new JWTProvider($request))->init();
        $response = $next($request);

        return $response;
    }
}
