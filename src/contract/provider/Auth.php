<?php
/**
 * FileName: Auth.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:22
 */

namespace coffin\jwtauth\contract\provider;


interface Auth
{
    /**
     * Check a user's credentials.
     *
     * @param array $credentials
     *
     * @return mixed
     */
    public function byCredentials(array $credentials);

    /**
     * Authenticate a user via the id.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function byId($id);

    /**
     * Get the currently authenticated user.
     *
     * @return mixed
     */
    public function user();
}