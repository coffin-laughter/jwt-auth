<?php
/**
 * FileName: InputSource.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:40
 */

namespace coffin\jwtauth\http\parser;

use think\Request;
use coffin\jwtauth\contract\http\Parser as ParserContract;

class InputSource implements ParserContract
{
    use KeyTrait;

    /**
     * Try to parse the token from the request input source.
     *
     * @param \think\Request $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        return $request->input($this->key);
    }
}