<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Game\GameModeStorage;
use ChessServer\Socket\WorkermanWebSocket;
use ChessServer\Socket\WorkermanClientStorage;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new WorkermanClientStorage(new GameModeStorage(), $logger);

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$server = (new WorkermanWebSocket($_ENV['WSS_PORT'], $_ENV['WSS_ADDRESS'], $context))
    ->init($clientStorage);

$server->run();
