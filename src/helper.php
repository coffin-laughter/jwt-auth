<?php

use think\App;
use think\Console;
use coffin\jwtauth\command\SecretCommand;
use coffin\jwtauth\provider\JWT as JWTProvider;

if (strpos(App::VERSION, '6.0') === false) {
    Console::addDefaultCommands([
        SecretCommand::class,
    ]);
    (new JWTProvider(app('request')))->init();
}
