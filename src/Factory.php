<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace coffin\jwtauth;

use coffin\jwtauth\claim\Claim;
use coffin\jwtauth\claim\Collection;
use coffin\jwtauth\support\RefreshFlow;
use coffin\jwtauth\support\CustomClaims;
use coffin\jwtauth\validator\PayloadValidator;
use coffin\jwtauth\claim\Factory as ClaimFactory;

class Factory
{
    use CustomClaims;
    use RefreshFlow;

    /**
     * The claim factory.
     *
     * @var Factory
     */
    protected $claimFactory;

    /**
     * The claims collection.
     *
     * @var Collection
     */
    protected $claims;

    /**
     * The default claims.
     *
     * @var array
     */
    protected $defaultClaims = [
        'iss',
        'iat',
        'exp',
        'nbf',
        'jti',
    ];

    /**
     * The validator.
     *
     * @var PayloadValidator
     */
    protected $validator;

    /**
     * Magically add a claim.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->addClaim($method, $parameters[0]);

        return $this;
    }

    /**
     * Constructor.
     *
     * @param Factory          $claimFactory
     * @param PayloadValidator $validator
     *
     * @return void
     */
    public function __construct(ClaimFactory $claimFactory, PayloadValidator $validator)
    {
        $this->claimFactory = $claimFactory;
        $this->validator = $validator;
        $this->claims = new Collection();
    }

    /**
     * Build and get the Claims Collection.
     *
     * @return Collection
     */
    public function buildClaimsCollection()
    {
        return $this->buildClaims()->resolveClaims();
    }

    /**
     * Empty the claims collection.
     *
     * @return $this
     */
    public function emptyClaims()
    {
        $this->claims = new Collection();

        return $this;
    }

    /**
     * Get the default claims.
     *
     * @return array
     */
    public function getDefaultClaims()
    {
        return $this->defaultClaims;
    }

    /**
     * Helper to get the ttl.
     *
     * @return int
     */
    public function getTTL()
    {
        return $this->claimFactory->getTTL();
    }

    /**
     * Create the Payload instance.
     *
     * @param bool $resetClaims
     *
     * @return Payload
     */
    public function make($resetClaims = false)
    {
        if ($resetClaims) {
            $this->emptyClaims();
        }

        return $this->withClaims($this->buildClaimsCollection());
    }

    /**
     * Set the default claims to be added to the Payload.
     *
     * @param array $claims
     *
     * @return $this
     */
    public function setDefaultClaims(array $claims)
    {
        $this->defaultClaims = $claims;

        return $this;
    }

    /**
     * Helper to set the ttl.
     *
     * @param int $ttl
     *
     * @return $this
     */
    public function setTTL($ttl)
    {
        $this->claimFactory->setTTL($ttl);

        return $this;
    }

    /**
     * Get the PayloadValidator instance.
     *
     * @return PayloadValidator
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * Get a Payload instance with a claims collection.
     *
     * @param Collection $claims
     *
     * @return Payload
     */
    public function withClaims(Collection $claims)
    {
        return new Payload($claims, $this->validator, $this->refreshFlow);
    }

    /**
     * Add a claim to the Payload.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    protected function addClaim($name, $value)
    {
        $this->claims->put($name, $value);

        return $this;
    }

    /**
     * Add an array of claims to the Payload.
     *
     * @param array $claims
     *
     * @return $this
     */
    protected function addClaims(array $claims)
    {
        foreach ($claims as $name => $value) {
            $this->addClaim($name, $value);
        }

        return $this;
    }

    /**
     * Build the default claims.
     *
     * @return $this
     */
    protected function buildClaims()
    {
        // remove the exp claim if it exists and the ttl is null
        if ($this->claimFactory->getTTL() === null && $key = array_search('exp', $this->defaultClaims)) {
            unset($this->defaultClaims[ $key ]);
        }

        // add the default claims
        foreach ($this->defaultClaims as $claim) {
            $this->addClaim($claim, $this->claimFactory->make($claim));
        }

        // add custom claims on top, allowing them to overwrite defaults
        return $this->addClaims($this->getCustomClaims());
    }

    /**
     * Build out the Claim DTO's.
     *
     * @return Collection
     */
    protected function resolveClaims()
    {
        return $this->claims->map(function ($value, $name) {
            return $value instanceof Claim ? $value : $this->claimFactory->get($name, $value);
        });
    }
}
