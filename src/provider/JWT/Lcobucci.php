<?php

namespace coffin\jwtauth\provider\JWT;

use Exception;
use ReflectionClass;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Signer\Ecdsa;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Keychain;
use coffin\jwtauth\exception\JWTException;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RS256;
use Lcobucci\JWT\Signer\Rsa\Sha384 as RS384;
use Lcobucci\JWT\Signer\Rsa\Sha512 as RS512;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HS256;
use Lcobucci\JWT\Signer\Hmac\Sha384 as HS384;
use Lcobucci\JWT\Signer\Hmac\Sha512 as HS512;
use Lcobucci\JWT\Signer\Ecdsa\Sha256 as ES256;
use Lcobucci\JWT\Signer\Ecdsa\Sha384 as ES384;
use Lcobucci\JWT\Signer\Ecdsa\Sha512 as ES512;
use coffin\jwtauth\exception\TokenInvalidException;

class Lcobucci extends Provider
{
    protected $builder;
    protected $parser;
    protected $signers
        = [
            'HS256' => HS256::class,
            'HS384' => HS384::class,
            'HS512' => HS512::class,
            'RS256' => RS256::class,
            'RS384' => RS384::class,
            'RS512' => RS512::class,
            'ES256' => ES256::class,
            'ES384' => ES384::class,
            'ES512' => ES512::class,
        ];

    public function __construct(Builder $builder, Parser $parser, $algo, $keys)
    {
        $this->builder = $builder;
        $this->parser = $parser;
        $this->algo = $algo;
        $this->keys = $keys;
        $this->signer = $this->getSign();
    }

    public function decode($token)
    {
        try {
            $jwt = $this->parser->parse($token);
        } catch (Exception $e) {
            throw new TokenInvalidException('Could not decode token: '
                . $e->getMessage(), $e->getCode(), $e);
        }

        if ( ! $jwt->verify($this->signer, $this->getVerificationKey())) {
            throw new TokenInvalidException('Token Signature could not be verified.');
        }

        return $jwt->getClaims();
    }


    public function encode(array $payload)
    {
        $this->builder->unsign();

        try {
            return (string) $this->builder->getToken($this->signer, $this->getSigningKey());
        } catch (Exception $e) {
            throw new JWTException(
                'Could not create token :' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }


    protected function getSign()
    {
        if ( ! isset($this->signers[ $this->algo ])) {
            throw new JWTException('Cloud not find ' . $this->algo . ' algo');
        }

        return new $this->signers[ $this->algo ]();
    }

    protected function getSigningKey()
    {
        return $this->isAsymmetric()
            ?
            (new Keychain())->getPrivateKey(
                $this->getPrivateKey(),
                $this->getPassword()
            )
            :
            $this->getSecret();
    }

    protected function getVerificationKey()
    {
        return $this->isAsymmetric()
            ?
            (new Keychain())->getPublicKey($this->getPublicKey())
            :
            $this->getSecret();
    }


    protected function isAsymmetric()
    {
        $reflect = new ReflectionClass($this->signer);

        return $reflect->isSubclassOf(Rsa::class)
            || $reflect->isSubclassOf(Ecdsa::class);
    }
}
