<?php
/**
 * FileName: AuthHeaders.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 09:34
 */

namespace coffin\jwtauth\http\parser;

use think\Request;
use coffin\jwtauth\contract\http\Parser as ParserContract;

class AuthHeader implements ParserContract
{
    /**
     * The header name.
     *
     * @var string
     */
    protected $header = 'authorization';

    /**
     * The header prefix.
     *
     * @var string
     */
    protected $prefix = 'bearer';

    /**
     * Try to parse the token from the request header.
     *
     * @param \think\Request $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        $header = $request->header($this->header) ?: $this->fromAltHeaders($request);

        if ($header !== null) {
            $position = strripos($header, $this->prefix);

            if ($position !== false) {
                $header = substr($header, $position + strlen($this->prefix));

                return trim(
                    strpos($header, ',') !== false ? strstr($header, ',', true) : $header
                );
            }
        }

        return null;
    }

    /**
     * Set the header name.
     *
     * @param string $headerName
     *
     * @return $this
     */
    public function setHeaderName($headerName)
    {
        $this->header = $headerName;

        return $this;
    }

    /**
     * Set the header prefix.
     *
     * @param string $headerPrefix
     *
     * @return $this
     */
    public function setHeaderPrefix($headerPrefix)
    {
        $this->prefix = $headerPrefix;

        return $this;
    }

    /**
     * Attempt to parse the token from some other possible headers.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null|string
     */
    protected function fromAltHeaders(Request $request)
    {
        return $request->server('HTTP_AUTHORIZATION') ?: $request->server('REDIRECT_HTTP_AUTHORIZATION');
    }
}
