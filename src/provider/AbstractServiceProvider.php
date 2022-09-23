<?php
/**
 * FileName: AbstractServiceProvider.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-22 10:32
 */

namespace coffin\jwtauth\provider;

use think\App;
use coffin\jwtauth\http\parser\Cookie;
use coffin\jwtauth\command\SecretCommand;
use coffin\jwtauth\http\parser\AuthHeader;
use coffin\jwtauth\http\parser\RouteParam;

abstract class AbstractServiceProvider
{
    private $app;
    private $config;
    private $request;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
    }

    public function register()
    {
        $this->registerLcobucciProvider();
        $this->registerStorageProvider();
        $this->registerJWTBlacklist();
        $this->registerManager();
        $this->registerTokenParser();
        $this->registerJWT();
        $this->registerJWTAuth();
        $this->registerPayloadValidator();
        $this->registerClaimFactory();
        $this->registerPayloadFactory();
        $this->registerJWTCommand();
    }

    protected function registerClaimFactory()
    {
        $this->app->make('coffin\jwtauth\claim\Factory', [
            $this->request,
        ])->setTTL($this->config['ttl'])->setRequest($this->config['leeway']);
    }

    protected function registerJWT()
    {
        $this->app->make('coffin\jwtauth\JWT', [
            $this->app->make('coffin\jwtauth\Manager'),
            $this->app->make('coffin\jwtauth\http\parser\Parser'),
        ]);
    }

    protected function registerJWTAuth()
    {
        $this->app->make('coffin\jwtauth\JWTAuth')
            ->lockSubject($this->config['lock_subject']);
    }

    protected function registerJWTBlacklist()
    {
        $this->app->make('coffin\jwtauth\Blacklist', [
            new $this->config['blacklist_storage'](),
        ])->setGracePeriod($this->config['blacklist_grace_period'])->setRefreshTTL($this->config['refresh_ttl']);
    }

    protected function registerJWTCommand()
    {
        $this->commands(SecretCommand::class);
    }

    protected function registerLcobucciProvider()
    {
        $this->app->make('coffin\jwtauth\provider\JWT\Lcobucci', [
            $this->config['secret'],
            $this->config['algo'],
            $this->config['keys'],
        ]);
    }

    protected function registerManager()
    {
        $this->app->make('coffin\jwtauth\Manager', [
            $this->app->make('coffin\jwtauth\provider\JWT\Lcobucci'),
            $this->app->make('coffin\jwtauth\Blacklist'),
            $this->app->make('coffin\jwtauth\Factory'),
        ])->setBlacklistEnabled((bool) $this->config['blacklist_enabled'])
            ->setPersistentClaims($this->config['persistent_claims']);
    }

    protected function registerPayloadFactory()
    {
        $this->app->make('coffin\jwtauth\Factory', [
            $this->app->make('coffin\jwtauth\claim\Factory'),
            $this->app->make('coffin\jwtauth\validator\PayloadValidator'),
        ]);
    }

    protected function registerPayloadValidator()
    {
        $this->app->make('coffin\jwtauth\validator\PayloadValidator')
            ->setRefreshTTL($this->config['refresh_ttl'])
            ->setRequiredClaims($this->config['required_claims']);
    }

    protected function registerStorageProvider()
    {
        $this->app->make('coffin\jwtauth\provider\storage\Tp6');
    }

    protected function registerTokenParser()
    {
        $this->app->make('coffin\jwtauth\http\parser\Parser', [
            $this->request,
            [
                new AuthHeader(),
                new Cookie(),
                new RouteParam(),
            ],
        ]);
    }
}
