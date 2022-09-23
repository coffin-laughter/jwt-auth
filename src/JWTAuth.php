<?php

namespace coffin\jwtauth;

class JWTAuth extends JWT
{
    public function auth()
    {
        return $this->getPayload()->toArray();
    }

    public function builder(array $user = [])
    {
        return $this->createToken($user);
    }
}
