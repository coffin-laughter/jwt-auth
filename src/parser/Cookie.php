<?php

namespace coffin\jwtauth\parser;

use think\Request;
use coffin\jwtauth\contract\Parser as ParserContract;

class Cookie implements ParserContract
{
    use KeyTrait;

    public function parse(Request $request)
    {
        return \think\facade\Cookie::get($this->key);
    }
}
