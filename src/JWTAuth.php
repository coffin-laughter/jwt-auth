<?php

namespace coffin\jwtauth;

use coffin\jwtauth\contract\provider\Auth;
use coffin\jwtauth\http\parser\Parser;

class JWTAuth extends JWT
{
    public function auth()
    {
        return (array) $this->getPayload();
    }

    public function builder(array $user = [])
    {
        return $this->createToken($user);
    }

    public function createToken($customerClaim = [])
    {
        return $this->manager->encode($customerClaim)->get();
    }

}
