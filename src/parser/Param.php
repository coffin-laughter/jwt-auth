<?php

namespace coffin\jwtauth\parser;

use think\Request;
use coffin\jwtauth\contract\Parser as ParserContract;

class Param implements ParserContract
{
    use KeyTrait;

    public function parse(Request $request)
    {
        return $request->param($this->key);
    }
}
