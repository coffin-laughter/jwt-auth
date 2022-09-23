<?php
/**
 * FileName: Parser.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:41
 */

namespace coffin\jwtauth\http\parser;

use \think\Request;

class Parser
{
    /**
     * The chain.
     *
     * @var array
     */
    private $chain;

    /**
     * The request.
     *
     * @var \think\Request
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param \think\Request $request
     * @param array          $chain
     *
     * @return void
     */
    public function __construct(Request $request, array $chain = [])
    {
        $this->request = $request;
        $this->chain = $chain;
    }

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * Add a new parser to the chain.
     *
     * @param array|\coffin\jwtauth\contract\http\Parser $parsers
     *
     * @return $this
     */
    public function addParser($parsers)
    {
        $this->chain = array_merge($this->chain, is_array($parsers) ? $parsers : [$parsers]);

        return $this;
    }

    /**
     * Set the order of the parser chain.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function setChain(array $chain)
    {
        $this->chain = $chain;

        return $this;
    }

    /**
     * Alias for setting the order of the chain.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function setChainOrder(array $chain)
    {
        return $this->setChain($chain);
    }

    /**
     * Iterate through the parsers and attempt to retrieve
     * a value, otherwise return null.
     *
     * @return string|null
     */
    public function parseToken()
    {
        foreach ($this->chain as $parser) {
            if ($response = $parser->parse($this->request)) {
                return $response;
            }
        }
    }

    /**
     * Check whether a token exists in the chain.
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->parseToken() !== null;
    }

    /**
     * Set the request instance.
     *
     * @param \think\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}