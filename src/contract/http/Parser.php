<?php
/**
 * FileName: Parser.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:21
 */

namespace coffin\jwtauth\contract\http;

use think\Request;

interface Parser
{
    /**
     * Parse the request.
     *
     * @param \think\Request $request
     *
     * @return null|string
     */
    public function parse(Request $request);
}
