<?php

namespace coffin\jwtauth;

use coffin\jwtauth\claim\JwtId;
use coffin\jwtauth\claim\Issuer;
use coffin\jwtauth\claim\Factory;
use coffin\jwtauth\claim\Subject;
use coffin\jwtauth\claim\Audience;
use coffin\jwtauth\claim\IssuedAt;
use coffin\jwtauth\claim\NotBefore;
use coffin\jwtauth\claim\Expiration;

class Payload
{
    protected $claims;

    protected $classMap
        = [
            'aud' => Audience::class,
            'exp' => Expiration::class,
            'iat' => IssuedAt::class,
            'iss' => Issuer::class,
            'jti' => JwtId::class,
            'nbf' => NotBefore::class,
            'sub' => Subject::class,
        ];
    protected $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function check($refresh = false)
    {
        $this->factory->validate($refresh);

        return $this;
    }

    public function customer(array $claim = [])
    {
        foreach ($claim as $key => $value) {
            $this->factory->customer(
                $key,
                is_object($value) ? $value->getValue() : $value
            );
        }

        return $this;
    }

    public function get()
    {
        $claim = $this->factory->builder()->getClaims();

        return $claim;
    }
}
