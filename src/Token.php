<?php


namespace coffin\jwtauth;

use coffin\jwtauth\validator\TokenValidator;

class Token
{
    /**
     * @var string
     */
    private $value;

    /**
     * Create a new JSON Web Token.
     *
     * @param string $value
     *
     * @return void
     */
    public function __construct($value)
    {
        $this->value = (string) (new TokenValidator())->check($value);
    }

    /**
     * Get the token.
     *
     * @return string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Get the token when casting to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
}