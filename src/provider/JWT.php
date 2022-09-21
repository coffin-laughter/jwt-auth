<?php

namespace coffin\jwtauth\provider;

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use think\App;
use think\Request;
use think\Container;
use think\facade\Config;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Builder;
use coffin\jwtauth\parser\Param;
use coffin\jwtauth\parser\Cookie;
use coffin\jwtauth\facade\JWTAuth;
use coffin\jwtauth\parser\AuthHeader;

class JWT
{
    private $config;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $config = require __DIR__ . '/../../config/config.php';
        if (strpos(App::VERSION, '6.0') !== false) {
            $this->config = array_merge($config, Config::get('jwt') ?? []);
        } else {
            $this->config = array_merge($config, Config::get('jwt.') ?? []);
        }
    }

    public function init()
    {
        $this->registerBlacklist();
        $this->registerDelayList();
        $this->registerProvider();
        $this->registerFactory();
        $this->registerPayload();
        $this->registerManager();
        $this->registerJWTAuth();
    }

    protected function registerBlacklist()
    {
        Container::getInstance()->make('coffin\jwtauth\Blacklist', [
            new $this->config['blacklist_storage'](),
        ]);
    }

    protected function registerDelayList()
    {
        Container::getInstance()->make('coffin\jwtauth\DelayList', [
            new $this->config['delaylist_storage'](),
            $this->config['delay_ttl'],
        ]);
    }

    protected function registerFactory()
    {
        Container::getInstance()->make('coffin\jwtauth\claim\Factory', [
            new Request(),
            $this->config['ttl'],
            $this->config['refresh_ttl'],
        ]);
    }

    protected function registerJWTAuth()
    {
        JWTAuth::parser()->setRequest($this->request)->setChain([
            new AuthHeader(),
            new Cookie(),
            new Param(),
        ]);
    }

    protected function registerManager()
    {
        Container::getInstance()->make('coffin\jwtauth\Manager', [
            Container::getInstance()->make('coffin\jwtauth\Blacklist'),
            Container::getInstance()->make('coffin\jwtauth\DelayList'),
            Container::getInstance()->make('coffin\jwtauth\Payload'),
            Container::getInstance()->make('coffin\jwtauth\provider\JWT\Lcobucci'),
        ]);
    }

    protected function registerPayload()
    {
        Container::getInstance()->make('coffin\jwtauth\Payload', [
            Container::getInstance()->make('coffin\jwtauth\claim\Factory'),
        ]);
    }


    protected function registerProvider()
    {
        //builder asymmetric keys
        $keys = $this->config['secret']
            ? $this->config['secret']
            : [
                'public'   => $this->config['public_key'],
                'private'  => $this->config['private_key'],
                'password' => $this->config['password'],
            ];
        Container::getInstance()->make('coffin\jwtauth\provider\JWT\Lcobucci', [
            new Builder(new JoseEncoder(),new ChainedFormatter()),
            new Parser(new JoseEncoder()),
            $this->config['algo'],
            $keys,
        ]);
    }
}
