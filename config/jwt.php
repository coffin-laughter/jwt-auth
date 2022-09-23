<?php
/**
 * FileName: jwt.php
 * ==============================================
 * Copy right 2016-2022
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: coffin_laughter | <chuanshuo_yongyuan@163.com>
 * @date  : 2022-09-21 18:08
 */


return [
    'secret'                 => env('JWT_SECRET'),
    'keys'                   => [
        'public'     => env('JWT_PUBLIC_KEY'),
        'private'    => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],
    //JWT time to live
    'ttl'                    => env('JWT_TTL', 60),
    //Refresh time to live
    'refresh_ttl'            => env('JWT_REFRESH_TTL', 20160),
    //
    'delay_ttl'              => env('JWT_DELAY_TTL', 3),
    //JWT hashing algorithm
    'algo'                   => env('JWT_ALGO', 'HS256'),
    //token获取方式，数组靠前值优先
    'token_mode'             => ['header', 'cookie', 'param'],
    'blacklist_storage'      => coffin\jwtauth\provider\storage\Tp6::class,
    'delaylist_storage'      => coffin\jwtauth\provider\storage\Tp6::class,
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    'algo'                   => env('JWT_ALGO', coffin\jwtauth\provider\JWT\Provider::ALGO_HS256),
    'required_claims'        => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],
    'persistent_claims'      => [],
    'lock_subject'           => true,
    'leeway'                 => env('JWT_LEEWAY', 0),
    'blacklist_enabled'      => env('JWT_BLACKLIST_ENABLED', true),
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),
];
