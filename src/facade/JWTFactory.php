<?php
/**
 * FileName: JWTFactory.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:27
 */

namespace coffin\jwtauth\facade;


use think\Facade;

class JWTFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'coffin\jwtauth\Factory';
    }
}