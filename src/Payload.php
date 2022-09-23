<?php

namespace coffin\jwtauth;

use Countable;
use ArrayAccess;
use JsonSerializable;
use BadMethodCallException;
use think\helper\Arr;
use coffin\jwtauth\claim\Claim;
use coffin\jwtauth\claim\Collection;
use coffin\jwtauth\exception\PayloadException;
use coffin\jwtauth\validator\PayloadValidator;

class Payload implements ArrayAccess, Countable, JsonSerializable
{
    /**
     * The collection of claims.
     *
     * @var Collection
     */
    private $claims;

    /**
     * Magically get a claim value.
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
        if (preg_match('/get(.+)\b/i', $method, $matches)) {
            foreach ($this->claims as $claim) {
                if (get_class($claim) === 'coffin\\jwtauth\\claim\\' . $matches[1]) {
                    return $claim->getValue();
                }
            }
        }

        throw new BadMethodCallException(sprintf('The claim [%s] does not exist on the payload.', array_reverse(explode($method, 'get', 2))[0]));
    }

    /**
     * Build the Payload.
     *
     * @param Collection       $claims
     * @param PayloadValidator $validator
     * @param bool             $refreshFlow
     *
     * @return void
     */
    public function __construct(Collection $claims, PayloadValidator $validator, $refreshFlow = false)
    {
        $this->claims = $validator->setRefreshFlow($refreshFlow)->check($claims);
    }

    /**
     * Invoke the Payload as a callable function.
     *
     * @param mixed $claim
     *
     * @return mixed
     */
    public function __invoke($claim = null)
    {
        return $this->get($claim);
    }

    /**
     * Get the payload as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Count the number of claims.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->toArray());
    }

    /**
     * Get the payload.
     *
     * @param mixed $claim
     *
     * @return mixed
     */
    public function get($claim = null)
    {
        $claim = value($claim);

        if ($claim !== null) {
            if (is_array($claim)) {
                return array_map([$this, 'get'], $claim);
            }

            return Arr::get($this->toArray(), $claim);
        }

        return $this->toArray();
    }

    /**
     * Get the array of claim instances.
     *
     * @return Collection
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * Get the underlying Claim instance.
     *
     * @param string $claim
     *
     * @return Claim
     */
    public function getInternal($claim)
    {
        return $this->claims->getByClaimName($claim);
    }

    /**
     * Determine whether the payload has the claim (by instance).
     *
     * @param Claim $claim
     *
     * @return bool
     */
    public function has(Claim $claim)
    {
        return $this->claims->has($claim->getName());
    }

    /**
     * Determine whether the payload has the claim (by key).
     *
     * @param string $claim
     *
     * @return bool
     */
    public function hasKey($claim)
    {
        return $this->offsetExists($claim);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Checks if a payload matches some expected values.
     *
     * @param array $values
     * @param bool  $strict
     *
     * @return bool
     */
    public function matches(array $values, $strict = false)
    {
        if (empty($values)) {
            return false;
        }

        $claims = $this->getClaims();

        foreach ($values as $key => $value) {
            if ( ! $claims->has($key) || ! $claims->get($key)->matches($value, $strict)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a payload strictly matches some expected values.
     *
     * @param array $values
     *
     * @return bool
     */
    public function matchesStrict(array $values)
    {
        return $this->matches($values, true);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($key)
    {
        return Arr::has($this->toArray(), $key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return Arr::get($this->toArray(), $key);
    }

    /**
     * Don't allow changing the payload as it should be immutable.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @throws PayloadException
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value)
    {
        throw new PayloadException('The payload is immutable');
    }

    /**
     * Don't allow changing the payload as it should be immutable.
     *
     * @param string $key
     *
     * @return void
     *
     * @throws PayloadException
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key)
    {
        throw new PayloadException('The payload is immutable');
    }

    /**
     * Get the array of claims.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->claims->toPlainArray();
    }

    /**
     * Get the payload as JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_SLASHES)
    {
        return json_encode($this->toArray(), $options);
    }
}
