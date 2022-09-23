<?php
/**
 * FileName: ThinkPHP6.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 10:13
 */

namespace coffin\jwtauth\provider\auth;


use coffin\jwtauth\contract\provider\Auth;

class ThinkPHP6 implements Auth
{

    public function byCredentials(array $credentials)
    {
    }

    public function byId($id)
    {
    }

    public function user()
    {
    }
}