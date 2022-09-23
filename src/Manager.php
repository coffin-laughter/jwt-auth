<?php

namespace coffin\jwtauth;

use coffin\jwtauth\claim\Collection;
use coffin\jwtauth\provider\JWT\Provider;
use coffin\jwtauth\support\CustomClaims;
use coffin\jwtauth\support\RefreshFlow;

// use coffin\jwtauth\contract\provider\JWT as JWTContract;
use coffin\jwtauth\provider\JWT\Lcobucci as JWTContract;
use think\helper\Arr;

class Manager
{
    use CustomClaims;
    use RefreshFlow;

    /**
     * The blacklist.
     *
     * @var Blacklist
     */
    protected $blacklist;

    /**
     * The blacklist flag.
     *
     * @var bool
     */
    protected $blacklistEnabled = true;

    /**
     * the payload factory.
     *
     * @var Factory
     */
    protected $payloadFactory;

    /**
     * the persistent claims.
     *
     * @var array
     */
    protected $persistentClaims = [];

    /**
     * The provider.
     *
     * @var \coffin\jwtauth\provider\JWT
     */
    protected $provider;

    /**
     * Constructor.
     *
     * @param \coffin\jwtauth\contract\provider\JWT $provider
     * @param Blacklist                             $blacklist
     * @param \coffin\jwtauth\Factory               $payloadFactory
     *
     * @return void
     */
    public function __construct(JWTContract $provider, Blacklist $blacklist, Factory $payloadFactory)
    {
        $this->provider = $provider;
        $this->blacklist = $blacklist;
        $this->payloadFactory = $payloadFactory;
    }

    /**
     * Decode a Token and return the Payload.
     *
     * @param \coffin\jwtauth\Token $token
     * @param bool                  $checkBlacklist
     *
     * @return \coffin\jwtauth\Payload
     *
     * @throws \coffin\jwtauth\Exceptions\TokenBlacklistedException
     */
    public function decode(Token $token, $checkBlacklist = true)
    {
        $payloadArray = $this->provider->decode($token->get());

        $payload = $this->payloadFactory
            ->setRefreshFlow($this->refreshFlow)
            ->customClaims($payloadArray)
            ->make();

        if ($checkBlacklist && $this->blacklistEnabled && $this->blacklist->has($payload)) {
            throw new TokenBlacklistedException('The token has been blacklisted');
        }

        return $payload;
    }

    /**
     * Encode a Payload and return the Token.
     *
     * @param \coffin\jwtauth\Payload $payload
     *
     * @return \coffin\jwtauth\Token
     */
    public function encode(Payload $payload)
    {
        $token = $this->provider->encode($payload->get());

        return new Token($token);
    }

    /**
     * Get the Blacklist instance.
     *
     * @return \coffin\jwtauth\Blacklist
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * Get the JWTProvider instance.
     *
     * @return \coffin\jwtauth\contract\provider\JWT
     */
    public function getJWTProvider()
    {
        return $this->provider;
    }

    /**
     * Get the Payload Factory instance.
     *
     * @return \coffin\jwtauth\Factory
     */
    public function getPayloadFactory()
    {
        return $this->payloadFactory;
    }

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @param \coffin\jwtauth\Token $token
     * @param bool                  $forceForever
     *
     * @return bool
     *
     * @throws \coffin\jwtauth\exception\JWTException
     */
    public function invalidate(Token $token, $forceForever = false)
    {
        if ( ! $this->blacklistEnabled) {
            throw new JWTException('You must have the blacklist enabled to invalidate a token.');
        }

        return call_user_func(
            [$this->blacklist, $forceForever ? 'addForever' : 'add'],
            $this->decode($token, false)
        );
    }

    /**
     * Refresh a Token and return a new Token.
     *
     * @param \coffin\jwtauth\Token $token
     * @param bool                  $forceForever
     * @param bool                  $resetClaims
     *
     * @return \coffin\jwtauth\Token
     */
    public function refresh(Token $token, $forceForever = false, $resetClaims = false)
    {
        $this->setRefreshFlow();

        $claims = $this->buildRefreshClaims($this->decode($token));

        if ($this->blacklistEnabled) {
            // Invalidate old token
            $this->invalidate($token, $forceForever);
        }

        // Return the new token
        return $this->encode(
            $this->payloadFactory->customClaims($claims)->make($resetClaims)
        );
    }

    /**
     * Set whether the blacklist is enabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setBlacklistEnabled($enabled)
    {
        $this->blacklistEnabled = $enabled;

        return $this;
    }

    /**
     * Set the claims to be persisted when refreshing a token.
     *
     * @param array $claims
     *
     * @return $this
     */
    public function setPersistentClaims(array $claims)
    {
        $this->persistentClaims = $claims;

        return $this;
    }

    /**
     * Build the claims to go into the refreshed token.
     *
     * @param \coffin\jwtauth\Payload $payload
     *
     * @return array
     */
    protected function buildRefreshClaims(Payload $payload)
    {
        // Get the claims to be persisted from the payload
        $persistentClaims = (new Collection($payload->toArray()))
            ->only($this->persistentClaims)
            ->toArray();

        // persist the relevant claims
        return array_merge(
            $this->customClaims,
            $persistentClaims,
            [
                'sub' => $payload['sub'],
                'iat' => $payload['iat'],
            ]
        );
    }
}
