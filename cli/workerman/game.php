<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\CommandContainer;
use ChessServer\Socket\Workerman\ClientStorage;
use ChessServer\Socket\Workerman\GameWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('game');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/game.log', Logger::INFO));

$parser = new CommandParser(new CommandContainer($logger));

$clientStorage = new ClientStorage($logger);

$socketName = "websocket://{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_GAME_PORT']}";

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$webSocket = (new GameWebSocket($socketName, $context, $parser))->init($clientStorage);

$webSocket->getWorker()->runAll();
