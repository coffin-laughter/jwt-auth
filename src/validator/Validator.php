<?php
/**
 * FileName: Validator.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 10:20
 */

namespace coffin\jwtauth\validator;

use coffin\jwtauth\exception\JWTException;
use coffin\jwtauth\support\RefreshFlow;
use coffin\jwtauth\contract\Validator as ValidatorContract;

abstract class Validator implements ValidatorContract
{
    use RefreshFlow;

    /**
     * Helper function to return a boolean.
     *
     * @param array $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        try {
            $this->check($value);
        } catch (JWTException $e) {
            return false;
        }

        return true;
    }

    /**
     * Run the validation.
     *
     * @param array $value
     *
     * @return void
     */
    abstract public function check($value);
}
