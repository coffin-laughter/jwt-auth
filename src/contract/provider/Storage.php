<?php
/**
 * FileName: Storage.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:23
 */

namespace coffin\jwtauth\contract\provider;


interface Storage
{
    public function set($key, $time = 0);

    public function get($key);

    public function delete($key);
}