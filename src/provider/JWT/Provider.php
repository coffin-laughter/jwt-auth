<?php

namespace coffin\jwtauth\provider\JWT;

abstract class Provider
{
    public const ALGO_ES256 = 'ES256';
    public const ALGO_ES384 = 'ES384';
    public const ALGO_ES512 = 'ES512';
    public const ALGO_HS256 = 'HS256';
    public const ALGO_HS384 = 'HS384';
    public const ALGO_HS512 = 'HS512';
    public const ALGO_RS256 = 'RS256';
    public const ALGO_RS384 = 'RS384';
    public const ALGO_RS512 = 'RS512';

    /**
     * The used algorithm.
     *
     * @var string
     */
    protected $algo;

    /**
     * The array of keys.
     *
     * @var array
     */
    protected $keys;

    /**
     * The secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Constructor.
     *
     * @param string $secret
     * @param string $algo
     * @param array  $keys
     *
     * @return void
     */
    public function __construct($secret, $algo, array $keys)
    {
        $this->secret = $secret;
        $this->algo = $algo;
        $this->keys = $keys;
    }

    /**
     * Get the algorithm used to sign the token.
     *
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * Get the array of keys used to sign tokens with an asymmetric algorithm.
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * Get the passphrase used to sign tokens
     * with an asymmetric algorithm.
     *
     * @return string|null
     */
    public function getPassphrase()
    {
        return Arr::get($this->keys, 'passphrase');
    }

    /**
     * Get the private key used to sign tokens with an asymmetric algorithm.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return Arr::get($this->keys, 'private');
    }

    /**
     * Get the public key used to sign tokens with an asymmetric algorithm.
     *
     * @return string|null
     */
    public function getPublicKey()
    {
        return Arr::get($this->keys, 'public');
    }

    /**
     * Get the secret used to sign the token.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the algorithm used to sign the token.
     *
     * @param string $algo
     *
     * @return $this
     */
    public function setAlgo($algo)
    {
        $this->algo = $algo;

        return $this;
    }

    /**
     * Set the keys used to sign the token.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function setKeys(array $keys)
    {
        $this->keys = $keys;

        return $this;
    }

    /**
     * Set the secret used to sign the token.
     *
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get the key used to sign the tokens.
     *
     * @return string|null
     */
    protected function getSigningKey()
    {
        return $this->isAsymmetric() ? $this->getPrivateKey() : $this->getSecret();
    }

    /**
     * Get the key used to verify the tokens.
     *
     * @return string|null
     */
    protected function getVerificationKey()
    {
        return $this->isAsymmetric() ? $this->getPublicKey() : $this->getSecret();
    }

    /**
     * Determine if the algorithm is asymmetric, and thus requires a public/private key combo.
     *
     * @return bool
     */
    abstract protected function isAsymmetric();
}
