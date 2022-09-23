<?php
/**
 * FileName: Collection.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:04
 */

namespace coffin\jwtauth\claim;


use coffin\jwtauth\claim\collect\Arr;
use coffin\jwtauth\claim\collect\Collection as CoffinCollection;
use think\helper\Str;

class Collection extends CoffinCollection
{
    /**
     * Create a new collection.
     *
     * @param mixed $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($this->getArrayableItems($items));
    }

    /**
     * Get a Claim instance by it's unique name.
     *
     * @param string   $name
     * @param callable $callback
     * @param mixed    $default
     *
     * @return \coffin\jwtauth\claim\Claim
     */
    public function getByClaimName($name, callable $callback = null, $default = null)
    {
        return $this->filter(function (Claim $claim) use ($name) {
            return $claim->getName() === $name;
        })->first($callback, $default);
    }

    /**
     * Validate each claim under a given context.
     *
     * @param string $context
     *
     * @return $this
     */
    public function validate($context = 'payload')
    {
        $args = func_get_args();
        array_shift($args);

        $this->each(function ($claim) use ($context, $args) {
            $str = Str::upper(Str::substr($context, 0, 1)) . Str::substr($context, 1);
            call_user_func_array(
                [$claim, 'validate' . $str],
                $args
            );
        });

        return $this;
    }

    /**
     * Determine if the Collection contains all of the given keys.
     *
     * @param mixed $claims
     *
     * @return bool
     */
    public function hasAllClaims($claims)
    {
        return count($claims) && (new static($claims))->diff($this->keys())->isEmpty();
    }

    /**
     * Get the claims as key/val array.
     *
     * @return array
     */
    public function toPlainArray()
    {
        return $this->map(function (Claim $claim) {
            return $claim->getValue();
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function getArrayableItems($items)
    {
        return $this->sanitizeClaims($items);
    }

    /**
     * Ensure that the given claims array is keyed by the claim name.
     *
     * @param mixed $items
     *
     * @return array
     */
    private function sanitizeClaims($items)
    {
        $claims = [];
        foreach ($items as $key => $value) {
            if ( ! is_string($key) && $value instanceof Claim) {
                $key = $value->getName();
            }

            $claims[ $key ] = $value;
        }

        return $claims;
    }

    public function map(callable $callback)
    {
        $keys = array_keys($this->items);

        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function only($keys)
    {
        if (is_null($keys)) {
            return new static($this->items);
        }

        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::only($this->items, $keys));
    }
}