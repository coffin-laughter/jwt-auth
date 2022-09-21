<?php


namespace coffin\jwtauth\contract;

use think\Request;

interface Parser
{
    public function parse(Request $request);
}
