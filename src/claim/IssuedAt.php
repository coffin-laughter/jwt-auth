<?php


namespace coffin\jwtauth\claim;

use coffin\jwtauth\exception\TokenExpiredException;

class IssuedAt extends Claim
{
    protected $name = 'iat';

    public function validatePayload()
    {
        if (time() < (int) $this->getValue()) {
            throw new TokenExpiredException('Issued At (iat) timestamp cannot be in the future.');
        }
    }

    public function validateRefresh($refreshTtl)
    {
        if (time() >= (int) $this->getValue() + $refreshTtl * 60) {
            throw new TokenExpiredException('Token has expired and can no longer be refreshed.');
        }
    }
}
