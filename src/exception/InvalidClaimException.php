<?php
/**
 * FileName: InvalidClaimException.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:01
 */

namespace coffin\jwtauth\exception;


use coffin\jwtauth\claim\Claim;

class InvalidClaimException extends JWTException
{
    /**
     * Constructor.
     *
     * @param  Claim  $claim
     * @param  int  $code
     * @param  \Exception|null  $previous
     * @return void
     */
    public function __construct(Claim $claim, $code = 0, Exception $previous = null)
    {
        parent::__construct('Invalid value provided for claim ['.$claim->getName().']', $code, $previous);
    }
}