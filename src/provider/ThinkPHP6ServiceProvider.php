<?php
/**
 * FileName: ThinkPHP6ServiceProvider.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 11:22
 */

namespace coffin\jwtauth\provider;


use think\facade\Config;

class ThinkPHP6ServiceProvider extends AbstractServiceProvider
{

    public function boot()
    {
        $config = require __DIR__ . '/../../config/jwt.php';
        $this->config = array_merge($config, Config::get('jwt') ?? []);
    }
}