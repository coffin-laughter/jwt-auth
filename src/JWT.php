<?php

namespace coffin\jwtauth;

use think\Request;
use coffin\jwtauth\http\parser\Parser;
use coffin\jwtauth\contract\JWTSubject;
use coffin\jwtauth\support\CustomClaims;
use coffin\jwtauth\exception\JWTException;

class JWT
{
    use CustomClaims;

    /**
     * Lock the subject.
     *
     * @var bool
     */
    protected $lockSubject = true;

    /**
     * The authentication manager.
     *
     * @var Manager
     */
    protected $manager;

    /**
     * The HTTP parser.
     *
     * @var Parser
     */
    protected $parser;

    /**
     * The token.
     *
     * @var Token|null
     */
    protected $token;

    /**
     * Magically call the JWT Manager.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->manager, $method)) {
            return call_user_func_array([$this->manager, $method], $parameters);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * JWT constructor.
     *
     * @param Manager $manager
     * @param Parser  $parser
     *
     * @return void
     */
    public function __construct(Manager $manager, Parser $parser)
    {
        $this->manager = $manager;
        $this->parser = $parser;
    }

    /**
     * Get the Blacklist.
     *
     * @return Blacklist
     */
    public function blacklist()
    {
        return $this->manager->getBlacklist();
    }

    public function createToken(array $customerClaim)
    {
        $payloadData = $this->customerClaim($customerClaim);
        return $this->manager->encode($payloadData)->get();
    }

    public function customerClaim(array $payloadData)
    {
        return $this->getPayloadFactory()->claims($payloadData)->make();
    }

    /**
     * Check that the token is valid.
     *
     * @param bool $getPayload
     *
     * @return Payload|bool
     */
    public function check($getPayload = false)
    {
        try {
            $payload = $this->checkOrFail();
        } catch (JWTException $e) {
            return false;
        }

        return $getPayload ? $payload : true;
    }

    /**
     * Alias to get the payload, and as a result checks that
     * the token is valid i.e. not expired or blacklisted.
     *
     * @return Payload
     *
     * @throws JWTException
     */
    public function checkOrFail()
    {
        return $this->getPayload();
    }

    /**
     * Check if the subject model matches the one saved in the token.
     *
     * @param string|object $model
     *
     * @return bool
     */
    public function checkSubjectModel($model)
    {
        if (($prv = $this->payload()->get('prv')) === null) {
            return true;
        }

        return $this->hashSubjectModel($model) === $prv;
    }

    /**
     * Get the Payload Factory.
     *
     * @return Factory
     */
    public function factory()
    {
        return $this->manager->getPayloadFactory();
    }

    /**
     * Generate a token for a given subject.
     *
     * @param JWTSubject $subject
     *
     * @return string
     */
    public function fromSubject(JWTSubject $subject)
    {
        $payload = $this->makePayload($subject);

        return $this->manager->encode($payload)->get();
    }

    /**
     * Alias to generate a token for a given user.
     *
     * @param JWTSubject $user
     *
     * @return string
     */
    public function fromUser(JWTSubject $user)
    {
        return $this->fromSubject($user);
    }

    /**
     * Convenience method to get a claim value.
     *
     * @param string $claim
     *
     * @return mixed
     */
    public function getClaim($claim)
    {
        return $this->payload()->get($claim);
    }

    /**
     * Get the raw Payload instance.
     *
     * @return Payload
     */
    public function getPayload()
    {
        $this->requireToken();

        return $this->manager->decode($this->token);
    }

    /**
     * Get the token.
     *
     * @return Token|null
     */
    public function getToken()
    {
        if ($this->token === null) {
            try {
                $this->parseToken();
            } catch (JWTException $e) {
                $this->token = null;
            }
        }

        return $this->token;
    }

    /**
     * Invalidate a token (add it to the blacklist).
     *
     * @param bool $forceForever
     *
     * @return $this
     */
    public function invalidate($forceForever = false)
    {
        $this->requireToken();

        $this->manager->invalidate($this->token, $forceForever);

        return $this;
    }

    /**
     * Set whether the subject should be "locked".
     *
     * @param bool $lock
     *
     * @return $this
     */
    public function lockSubject($lock)
    {
        $this->lockSubject = $lock;

        return $this;
    }

    /**
     * Create a Payload instance.
     *
     * @param JWTSubject $subject
     *
     * @return Payload
     */
    public function makePayload(JWTSubject $subject)
    {
        return $this->factory()->customClaims($this->getClaimsArray($subject))->make();
    }

    /**
     * Get the Manager instance.
     *
     * @return Manager
     */
    public function manager()
    {
        return $this->manager;
    }

    /**
     * Get the Parser instance.
     *
     * @return Parser
     */
    public function parser()
    {
        return $this->parser;
    }

    /**
     * Parse the token from the request.
     *
     * @return $this
     *
     * @throws JWTException
     */
    public function parseToken()
    {
        if ( ! $token = $this->parser->parseToken()) {
            throw new JWTException('The token could not be parsed from the request');
        }

        return $this->setToken($token);
    }

    /**
     * Alias for getPayload().
     *
     * @return Payload
     */
    public function payload()
    {
        return $this->getPayload();
    }

    /**
     * Refresh an expired token.
     *
     * @param bool $forceForever
     * @param bool $resetClaims
     *
     * @return string
     */
    public function refresh($forceForever = false, $resetClaims = false)
    {
        $this->requireToken();

        return $this->manager->customClaims($this->getCustomClaims())
            ->refresh($this->token, $forceForever, $resetClaims)
            ->get();
    }

    /**
     * Set the request instance.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->parser->setRequest($request);

        return $this;
    }

    /**
     * Set the token.
     *
     * @param Token|string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token instanceof Token ? $token : new Token($token);

        return $this;
    }

    /**
     * Unset the current token.
     *
     * @return $this
     */
    public function unsetToken()
    {
        $this->token = null;

        return $this;
    }

    /**
     * Build the claims array and return it.
     *
     * @param JWTSubject $subject
     *
     * @return array
     */
    protected function getClaimsArray(JWTSubject $subject)
    {
        return array_merge(
            $this->getClaimsForSubject($subject),
            $subject->getJWTCustomClaims(), // custom claims from JWTSubject method
            $this->customClaims // custom claims from inline setter
        );
    }

    /**
     * Get the claims associated with a given subject.
     *
     * @param JWTSubject $subject
     *
     * @return array
     */
    protected function getClaimsForSubject(JWTSubject $subject)
    {
        return array_merge([
            'sub' => $subject->getJWTIdentifier(),
        ], $this->lockSubject ? ['prv' => $this->hashSubjectModel($subject)] : []);
    }

    /**
     * Hash the subject model and return it.
     *
     * @param string|object $model
     *
     * @return string
     */
    protected function hashSubjectModel($model)
    {
        return sha1(is_object($model) ? get_class($model) : $model);
    }

    /**
     * Ensure that a token is available.
     *
     * @return void
     *
     * @throws JWTException
     */
    protected function requireToken()
    {
        if ( ! $this->token) {
            throw new JWTException('A token is required');
        }
    }
}
